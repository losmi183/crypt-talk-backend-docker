<?php

namespace App\Repository;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Connection;
use App\Services\ConversationServices;
use App\Services\JWTServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use stdClass;

class UserRepository
{
    private JWTServices $jwtServices;
    public ConversationRepository $conversationRepository;

    public function __construct(JWTServices $jwtServices, ConversationRepository $conversationRepository) {
        $this->jwtServices = $jwtServices;
        $this->conversationRepository = $conversationRepository;
    }

    /**
     * @param array $data
     * 
     * @return User
     */
    public function store(array $data): User
    {
        try {
            $user = User::create($data);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            abort(400, 'User not registered');
        }
        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function search(array $data): stdClass
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];
        $search = $data['search'];
        $result = new stdClass;

        $result->users = DB::table('users as u')
            ->where('u.id', '!=', $user_id)
            ->where(function($query) use ($search) {
                $query->where('u.name', 'like', '%' . $search . '%')
                    ->orWhere('u.email', 'like', '%' . $search . '%');
            })
            ->whereNotExists(function ($query) use ($user_id) {
                $query->select(DB::raw(1))
                    ->from('conversation_user as cu1')
                    ->join('conversation_user as cu2', 'cu1.conversation_id', '=', 'cu2.conversation_id')
                    ->join('conversations as c', 'c.id', '=', 'cu1.conversation_id')
                    ->where('c.type', 'private') // ← KLJUČNO
                    ->whereColumn('cu1.user_id', 'u.id')
                    ->where('cu2.user_id', $user_id);
            })
            ->select(
                'u.id', 
                'u.name', 
                'u.email', 
                DB::raw("CONCAT('" . config('app.url') . "/images/avatar/', COALESCE(u.avatar, 'default.png')) as avatar_url"))
            ->limit(10)
            ->get();

        $result->conversations = $this->conversationRepository->userConversations($user_id, $search);

        return $result;
    }

    public function show(int $id): ?User
    {
        return User::find($id);
    }
}