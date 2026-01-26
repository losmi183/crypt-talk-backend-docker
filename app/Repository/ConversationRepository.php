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

class ConversationRepository
{
    private JWTServices $jwtServices;  

    public function __construct(JWTServices $jwtServices) {
        $this->jwtServices = $jwtServices;
    }

   public function userConversations(int $user_id, ?string $search = null)
   {
    $query = DB::table(table: 'conversations as c')        
        ->join('conversation_user as cu_user', function($join) use ($user_id) {
            $join->on('c.id', '=', 'cu_user.conversation_id')
                ->where('cu_user.user_id', $user_id);
        })
        ->leftjoin('conversation_user as cu', function($join) use ($user_id) {
            $join->on('c.id', '=', 'cu.conversation_id')
                ->where('cu.user_id', '!=', $user_id);
        })
        ->leftJoin('users as u', 'u.id', 'cu.user_id');


        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('u.name', 'like', "%{$search}%") // Pretraga po imenu sagovornika
                ->orWhere('c.title', 'like', "%{$search}%"); // Ili po nazivu grupe
            });
        }

        $conversations = $query->select([
            'c.id', 'c.title', 'c.type', 'c.encrypted', 'c.salt', 'c.iterations',
            DB::raw("GROUP_CONCAT(CONCAT_WS('|', u.id, u.name, u.avatar)) as participants"),

            DB::raw("
                (SELECT COUNT(*) 
                FROM messages as m
                WHERE m.conversation_id = c.id
                    AND (
                        cu_user.last_read_message_id IS NULL
                        OR m.id > cu_user.last_read_message_id
                    )
                ) as unread_count
            ")            
        ])        
        ->groupBy('c.id', 'c.title', 'c.type', 'c.encrypted', 'c.salt', 'c.iterations', 'cu_user.last_read_message_id')
        ->get();

        foreach ($conversations as $conversation) {
            $conversation->users = [];
            $participants = explode(',' ,$conversation->participants);
            foreach ($participants as $p) {
                $pArray = explode('|', $p);
                $pObj = new stdClass;
                $pObj->id = $pArray[0] ?? null;
                $pObj->name = $pArray[1] ?? null;
                $pObj->avatar = $pArray[2] ?? null;
                $pObj->avatar_url = config('app.url') . '/images/avatar/' . ($pObj->avatar ? $pObj->avatar : 'default.png');
                $conversation->users[] = $pObj;
            }
        }

        return $conversations;
   }
}