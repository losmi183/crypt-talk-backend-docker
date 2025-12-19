<?php

namespace App\Services;
use App\Models\User;
use App\Models\Connection;
use App\Repository\UserRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class ConnectionServices {

    private UserRepository $userRepository;
    private JWTServices $jwtServices;
    private PusherServices $pusherServices;

    public function __construct(UserRepository $userRepository, JWTServices $jwtServices, PusherServices $pusherServices) {
        $this->userRepository = $userRepository;
        $this->jwtServices = $jwtServices;
        $this->pusherServices = $pusherServices;
    }

    public function myConnections(): Collection
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];

        $friends = DB::table('users as u')
            ->join('user_connections as c', function ($join) {
                $join->on('u.id', '=', 'c.initiator_id')
                    ->orOn('u.id', '=', 'c.recipient_id');
            })
            ->where(function ($query) use ($user_id) {
                $query->where('c.initiator_id', $user_id)
                    ->orWhere('c.recipient_id', $user_id);
            })
            ->where('u.id', '!=', $user_id)
            ->whereNotNull('c.accepted_at') // Samo prihvaćeni prijatelji
            ->select('u.id', 'u.name')
            ->distinct()
            ->get();

        $friend_ids = $friends->pluck('id');

        $messages = DB::table('messages')
            ->select('sender_id', 'updated_at', 'seen', 'message')
            ->whereIn('sender_id', $friend_ids)
            ->orWhereIn('receiver_id', $friend_ids)
            ->where('receiver_id', $user_id)
            // ->whereNull('seen')
            ->get(); // Get umesto pluck da bi imao updated_at

        foreach ($friends as $friend) {
            $friend->new_messages = 0;
            $friend->last_message_time = '1970-01-01 00:00:00';
            
            foreach ($messages as $message) {
                if ($message->sender_id == $friend->id && $message->seen === null) {
                    $friend->new_messages++;     
                }
                // Postavi last_message_time ako je poruka novija
                if ($message->sender_id == $friend->id && $message->updated_at > $friend->last_message_time) {
                    $friend->last_message_time = $message->updated_at;
                }
            }
        }

        // Sortiraj prijatelje po last_message_time (najnovije prvo, NULL na kraju)
        $friends = $friends->sortByDesc(function ($friend) {
            return $friend->last_message_time ?? '1970-01-01 00:00:00';
        })->values();

        return $friends;
    }

    public function requested(): Collection
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];

        $friends = DB::table('user_connections as c')
            ->leftJoin('users as u', 'c.initiator_id', '=', 'u.id')
            ->where('c.recipient_id', '=', $user_id)
            ->whereNull('c.accepted_at') // Ispravljeno
            ->select('c.id as connection_id', 'u.id as user_id', 'u.name', 'u.email')
            ->distinct()
            ->get();

        return $friends;
    }

   
    public function initiate(int $recipient_id): Connection
    {
        $initiator = $this->jwtServices->getContent();

        // Check if connection already exists in either direction
        $connection = DB::table('user_connections')
            ->where(function($query) use ($initiator, $recipient_id) {
                $query->where('initiator_id', $initiator['id'])
                    ->where('recipient_id', $recipient_id);
            })
            ->orWhere(function($query) use ($initiator, $recipient_id) {
                $query->where('initiator_id', $recipient_id)
                    ->where('recipient_id', $initiator['id']);
            })
            ->first();

        if ($connection) {
            abort(403, 'Connection already exists');
        }

        try {
            $connection = Connection::create([
                'initiator_id' => $initiator['id'],
                'recipient_id' => $recipient_id
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            abort(500, 'Database error');
        }

        return $connection;
    }

    public function accept(int $connection_id): Connection
    {
        $user = $this->jwtServices->getContent();        

        try {
            $connection = Connection::where('recipient_id', $user['id'])->whereNull('accepted_at')->first();
            if (!$connection) {
                abort(404, 'Connection not found or already accepted');
            }

            $connection->accepted_at = now();
            $connection->salt = bin2hex(random_bytes(16));
            $connection->save();

            $event = 'connection.accepted';
            $message = $user['name'].' accepted your connection request';

            $this->pusherServices->push($event, $connection->initiator_id, $message, $user);
            

            return Connection::find($connection_id);
        } catch (QueryException $e) {
            Log::error("DB error in accept(): " . $e->getMessage());
            abort(500, 'Database error');
        }
    }

    public function reject(int $connection_id):bool
    {
        $recipient = $this->jwtServices->getContent();

        try {
            $deleted = Connection::where('id', $connection_id)
                ->where('recipient_id', $recipient['id'])
                ->delete();

            if (!$deleted) {
                abort(response()->json(['error' => 'Connection not found'], 404));
            }

            return true;
        } catch (QueryException $e) {
            Log::error("DB error in reject(): " . $e->getMessage());
            abort(response()->json(['error' => 'Database error: ' . $e->getMessage()], 500));
        }
    }
    public function delete(int $connection_id): bool
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];

        try {
            $deleted = Connection::where('id', $connection_id)
                ->where(function ($query) use ($user_id) {
                    $query->where('initiator_id', $user_id)
                        ->orWhere('recipient_id', $user_id);
                })
                ->delete();

            if (!$deleted) {
                abort(response()->json(['error' => 'Connection not found'], 404));
            }

            return true;
        } catch (QueryException $e) {
            Log::error("DB error in delete(): " . $e->getMessage());
            abort(response()->json(['error' => 'Database error: ' . $e->getMessage()], 500));
        }
    }
}