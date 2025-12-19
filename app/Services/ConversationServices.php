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

        $conversations = Conversation::forUser($user_id)
            ->get(['id', 'type', 'title']);

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
        $last_message_id = $data['lastMessageId'] ? intval($data['lastMessageId']) : null;

        $conversation = Conversation::with(['users' => function($q) use ($user_id) {
            
            $q->where('users.id', '!=', $user_id)
            ->select('users.id', 'users.name', 'avatar', 'conversation_user.last_read_message_id');
        }])
        ->first();

        $conversation->messages = DB::table('messages as m')
        ->join('users as u', 'u.id', 'm.sender_id')
        ->select('m.*', 'u.name as sender_name')
        ->where('m.conversation_id', $conversation_id)
        ->orderBy('m.id','desc')
        ->get();
        
        return $conversation;
    }

    public function send(int $conversation_id, string $content): Message
    {
        $user = $this->jwtServices->getContent();
        unset($user['exp']);
        $event = 'message.sent';
        $channel = config('pusher.PRIVATE_CONVERSATION').$conversation_id;

        $this->pusherServices->push(
            $event,
            $channel,
            $conversation_id, 
            $content, 
            $user);

        try {
            $message = Message::create([
                'sender_id' => $user['id'],
                'conversation_id' => $conversation_id,
                'message' => $content,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
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

    public function markAsSeen(int $friend_id): bool
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];

        try {
            DB::table('messages')
                ->where('sender_id', $friend_id)
                ->where('receiver_id', $user_id)
                ->where('is_read', 0)
                ->update([
                    'status' => 'seen',
                    'is_read' => 1,
                    'seen' => now(),
                ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return true;
    }
}