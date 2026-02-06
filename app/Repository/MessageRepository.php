<?php

namespace App\Repository;

use App\Models\User;
use App\Models\Message;
use App\Models\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use stdClass;

class MessageRepository
{
   public function save(array $data): Message
   {
       return Message::create($data);
   }

   public function getMessage($message_id): stdClass
   {
        $media_link = config('app.url') . '/api/media/';

        return DB::table('messages as m')
        ->join('users as u', 'u.id', 'm.sender_id')
        ->leftJoin('attachments as a', function($join) {
            $join->on('a.message_id', '=', 'm.id')
                ->where('m.type', '=', 'attachment');
        })
        ->select(
            'm.*', 
            'u.name as sender_name', 
            DB::raw("CONCAT('" . config('app.url') . "/images/avatar/', u.avatar) as avatar_url"),
            DB::raw("CONCAT('" . $media_link . "', a.thumbnail) as thumbnail"),
            DB::raw("CONCAT('" . $media_link . "', a.path) as attachment_path"),
            'a.type as attachment_type',
            'a.duration'
        )
        ->where('m.id', $message_id)
        ->first();
   }

   public function getParticipants(int $conversation_id, int $user_id): Collection
   {
        return DB::table('conversation_user')
            ->select('user_id')
            ->where('conversation_id', $conversation_id)
            ->where('user_id', '!=', $user_id)
            ->get();
   }

   public function saveMessage($data): int
   {
        try {
            $messageId = DB::table('messages')->insertGetId([
                'sender_id' => $data['user_id'],
                'conversation_id' => $data['conversationId'],
                'is_encrypted' => $data['isEncrypted'],
                'message' => $data['text'] ?? null,
                'message_encrypted' => $data['encryptedData'] ?? null,
                'iv' => $data['iv'] ?? null
            ]);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
        return $messageId;
   }

   public function formatMessage(int $messageId)
   {
        return DB::table('messages as m')
            ->join('users as u', 'u.id', 'm.sender_id')
            ->select(
                'm.*', 
                'u.name as sender_name', 
                DB::raw("CONCAT('" . config('app.url') . "/images/avatar/', u.avatar) as avatar_url")
            )
            ->where('m.id', $messageId)
            ->first();
   }


   public function getConversationMessages(int $conversationId, int $limit = 20): array
    {
        return DB::table('messages')
            ->where('conversation_id', $conversationId)
            ->where('type', 'message')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            // ->reverse() // Vrati u hronološki redosled
            ->values()
            ->toArray();
    }
}