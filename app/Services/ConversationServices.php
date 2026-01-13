<?php

namespace App\Services;
use stdClass;
use Carbon\Carbon;
use Pusher\Pusher;
use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Services\PusherServices;
use App\Repository\UserRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Repository\MessageRepository;

class ConversationServices {

    private UserRepository $userRepository;
    private JWTServices $jwtServices;
    private MessageRepository $messageRepository;
    private PusherServices $pusherServices;
    public function __construct(UserRepository $userRepository, JWTServices $jwtServices, MessageRepository $messageRepository, PusherServices $pusherServices) {
        $this->userRepository = $userRepository;
        $this->jwtServices = $jwtServices;
        $this->messageRepository = $messageRepository;
        $this->pusherServices = $pusherServices;
    }

    public function index() {

        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];

        $conversations = DB::table('conversations as c')
        
        ->join('conversation_user as cu_user', function($join) use ($user_id) {
            $join->on('c.id', '=', 'cu_user.conversation_id')
                ->where('cu_user.user_id', $user_id);
        })

        ->leftjoin('conversation_user as cu', function($join) use ($user_id) {
            $join->on('c.id', '=', 'cu.conversation_id')
                ->where('cu.user_id', '!=', $user_id);
        })

        ->leftJoin('users as u', 'u.id', 'cu.user_id')

        ->select([
            'c.id', 'c.title', 'c.type',
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
        ->groupBy('c.id', 'c.title', 'c.type', 'cu_user.last_read_message_id')
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


        // $conversations = Conversation::forUser($user_id)
        //     ->leftJoin('conversation_user as cu', function($join) use ($user_id) {
        //         $join->on('cu.conversation_id', 'conversations.id')
        //             ->where('cu.user_id', $user_id);
        //     })
        //     ->leftJoin('messages as m', function($join) {
        //         // COALESCE uzima 0 ako je last_read_message_id null ili 0
        //         $join->on('m.conversation_id', 'conversations.id')
        //             ->whereRaw('m.id > COALESCE(cu.last_read_message_id, 0)');
        //     })
        //     ->select(
        //         'conversations.id',
        //         'conversations.type',
        //         'conversations.title',
        //         DB::raw('COUNT(m.id) as unread_count')
        //     )
        //     ->groupBy('conversations.id', 'conversations.type', 'conversations.title')
        //     ->get();


        return $conversations;
    }

    public function startConversation($friend_id): Conversation 
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];


        $conversation = Conversation::where('type', 'private')
            ->whereHas('users', fn($q) => $q->where('users.id', $user_id))
            ->whereHas('users', fn($q) => $q->where('users.id', $friend_id))
            ->forUser($user_id)     // tvoj scope ovde
            ->first(['id', 'type', 'title']);


        if($conversation) {
            return $conversation;
        }

        $conversation_id = DB::table('conversations')->insertGetId([
            'type' => 'private',
            'salt' => bin2hex(random_bytes(16)),
            'created_by' => $user_id
        ]);

        DB::table('conversation_user')->insert([
            [
                'conversation_id' => $conversation_id,
                'user_id' => $user_id,
                'joined_at' => now()
            ],
            [
                'conversation_id' => $conversation_id,
                'user_id' => $friend_id,
                'joined_at' => now()
            ]
        ]);

        $conversation = Conversation::forUser($user_id)
            ->where('id', $conversation_id)
            ->first(['id', 'type', 'title']);

        return $conversation;
    }

    public function show(array $data)
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];
        $conversation_id = intval($data['conversationId']);
        $limit = 20;
        $last_message_id = $data['lastMessageId'] ? intval($data['lastMessageId']) : null;
        $media_link = config('app.url') . '/api/media/';
        $attachment_path = config('app.url') . '/storage/';

        $conversation = Conversation::with(['users' => function($q) use ($user_id) {
            
            $q->where('users.id', '!=', $user_id)
            ->select('users.id', 'users.name', 'avatar', 'conversation_user.last_read_message_id');
        }])
        ->first();

        $messagesQuery = DB::table('messages as m')
        ->join('users as u', 'u.id', 'm.sender_id')
        ->leftJoin('attachments as a', function($join) {
            $join->on('a.message_id', '=', 'm.id')
                ->where('m.type', '=', 'attachment');
        })
        ->select(
            'm.*', 
            'u.name as sender_name', 
            DB::raw("CONCAT('" . config('app.url') . "/images/avatar/', COALESCE(NULLIF(u.avatar, ''), 'default.png')) as avatar_url"),
            DB::raw("CONCAT('" . $attachment_path . "', a.path) as attachment_path"),
            DB::raw("CONCAT('" . $media_link . "', a.thumbnail) as thumbnail"),
            'a.type as attachment_type',
        )
        ->where('m.conversation_id', $conversation_id);

        if($last_message_id) {
            $messagesQuery->where('m.id', '<', $last_message_id);
        }
        
        $conversation->messages = $messagesQuery
        ->orderBy('m.id','desc')
        ->limit($limit)
        ->get()
        ->values();
        
        return $conversation;
    }

    public function sendMessage(int $conversation_id, string $content): stdClass
    {
        $user = $this->jwtServices->getContent();
        unset($user['exp']);
        $event = 'message.sent';

        try {
            $messageId = DB::table('messages')->insertGetId([
                'sender_id' => $user['id'],
                'conversation_id' => $conversation_id,
                'message' => $content,
            ]);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        $message = DB::table('messages as m')
            ->join('users as u', 'u.id', 'm.sender_id')
            ->select(
                'm.*', 
                'u.name as sender_name', 
                DB::raw("CONCAT('" . config('app.url') . "/images/avatar/', u.avatar) as avatar_url")
            )
            ->where('m.id', $messageId)
            ->first();

        // $conversation = DB::table('conversations')->where('id', $conversation_id)->first();
        $participants = DB::table('conversation_user')
        ->select('user_id')
        ->where('conversation_id', $conversation_id)
        ->where('user_id', '!=', $user['id'])
        ->get();

        foreach ($participants as $participant) {
            $channel = config('pusher.PRIVATE_CONVERSATION').$participant->user_id;
            $this->pusherServices->push(
                $event,
                $channel,
                $conversation_id, 
                $message, 
            );
        }

        return $message;
    }

    public function seen(int $friend_id, string $seen): bool
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];
        $event = 'message.seen';

        $pusher = new Pusher(
            config('pusher.key'),
            config('pusher.secret'),
            config('pusher.app_id'),
            [
                'cluster' => config('pusher.cluster'),
                'useTLS' => config('pusher.useTLS', true),
            ]
        );

        // Privatni kanal za korisnika recipient_id
        $pusher->trigger("private-conversation-{$friend_id}", $event, [
            'seen' => $seen
        ]);

        try {
            Message::where(function ($q) use ($user_id, $friend_id) {
            $q->where('sender_id', $friend_id)
            ->where('receiver_id', $user_id);
        })
        ->update([
            'seen' => Carbon::parse($seen)->toDateTimeString()
        ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return true;
    }

    public function markAsRead(array $data): bool
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];
        $conversationId = $data['conversationId'];
        $lastMessageId = isset($data['messageId']) ? $data['messageId'] : null;

        if(!$lastMessageId) {
            $lastMessageId = DB::table('messages')
                ->where('conversation_id', $conversationId)
                ->latest('id')
                ->value('id');
        }

        try {
            DB::table('conversation_user')
                ->where('conversation_id', $conversationId)
                ->where('user_id', $user_id)
                ->update([
                    'last_read_message_id' => $lastMessageId
                ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return true;
    }
}