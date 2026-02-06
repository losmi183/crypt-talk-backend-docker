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
use App\Services\GroqServices;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Repository\MessageRepository;
use App\Repository\ConversationRepository;

class ConversationServices {

    private UserRepository $userRepository;
    private JWTServices $jwtServices;
    private MessageRepository $messageRepository;
    private ConversationRepository $conversationRepository;
    private PusherServices $pusherServices;
    private GroqServices $groqServices;
    public function __construct(
        UserRepository $userRepository, 
        JWTServices $jwtServices, 
        MessageRepository $messageRepository, 
        PusherServices $pusherServices,
        ConversationRepository $conversationRepository,
        GroqServices $groqServices
    ) {
        $this->userRepository = $userRepository;
        $this->jwtServices = $jwtServices;
        $this->messageRepository = $messageRepository;
        $this->pusherServices = $pusherServices;
        $this->conversationRepository = $conversationRepository;
        $this->groqServices = $groqServices;
    }

    public function index() : stdClass
    {

        $user = $this->jwtServices->getContent();
        $result = new stdClass;
        $result->conversations = $this->conversationRepository->userConversations($user['id']);

        $messages = DB::table('messages as m')
        ->leftJoin('conversation_user as cu', 'cu.conversation_id', '=', 'm.conversation_id')
        ->where('cu.user_id', $user['id'])
        ->get();

        $result->totalMessages = count($messages);
        $result->encryptedMessages = 0;
        
        foreach ($messages as $message) {
            if($message->is_encrypted) {
                $result->encryptedMessages++;
            }
        }
        return $result;
    }

    public function startConversation($friend_id): int 
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];

        // 1. Proveri da li već postoji privatna konverzacija između ova dva usera
        $existingConversationId = DB::table('conversation_user as cu1')
            ->join('conversation_user as cu2', 'cu1.conversation_id', '=', 'cu2.conversation_id')
            ->join('conversations as c', 'c.id', '=', 'cu1.conversation_id')
            ->where('c.type', 'private')
            ->where('cu1.user_id', $user_id)
            ->where('cu2.user_id', $friend_id)
            ->value('c.id'); // Vraća samo ID ako postoji, inače null

        if ($existingConversationId) {
            // Ako postoji, vrati tu konverzaciju (koristi tvoj Eloquent model)
            return $existingConversationId;
        }

        // 2. Ako ne postoji, kreiraj novu
        return DB::transaction(function () use ($user_id, $friend_id) {
            // Kreiraj zapis u conversations
            $newId = DB::table('conversations')->insertGetId([
                'type' => 'private',
                'salt' => bin2hex(random_bytes(16)),
                'iterations' => config('crypto.pbkdf2_iterations'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Ubaci oba korisnika u pivot tabelu
            DB::table('conversation_user')->insert([
                ['conversation_id' => $newId, 'user_id' => $user_id, 'joined_at' => now(), 'created_at' => now()],
                ['conversation_id' => $newId, 'user_id' => $friend_id, 'joined_at' => now(), 'created_at' => now()],
            ]);

            return $newId;
        });
    }

    // public function getCoversation(int $conversation_id): stdClass
    // {
    //     return DB::table('conversations as c')
    //     ->leftJoin('conversation_user as cu', 'cu.conversation_id', 'c.id')
    //     ->leftJoin('users as u', 'u.id', 'cu.user_id')
    //     ->where('c.id', $conversation_id)
    //     ->select([
    //         ''
    //     ])
    // }

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
            DB::raw("CONCAT('" . $media_link . "', a.path) as attachment_path"),
            DB::raw("CONCAT('" . $media_link . "', a.thumbnail) as thumbnail"),
            'a.type as attachment_type',
            'a.duration'
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

    public function changeEncrypted(array $data)
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];
        $conversation_id = intval($data['conversationId']);
        $encrypted = $data['encrypted'];

        // 1. Provera da li korisnik učestvuje
        $conversation = DB::table('conversations as c')
            ->leftJoin('conversation_user as cu', 'c.id', 'cu.conversation_id')
            ->where('c.id', $conversation_id)
            ->where('cu.user_id', $user_id)
            ->first();

        if (!$conversation) {
            abort(404, 'User not in conversation');
        }

        // 2. Izvrši update
        DB::table('conversations')
            ->where('id', $conversation_id)
            ->update(['encrypted' => $encrypted]);

        // 4. Vrati success
        return response()->json(['success' => true]);
    }

    public function sendMessage(array $data): array
    {
        $array = [];
        $user = $this->jwtServices->getContent();
        unset($user['exp']);
        $event = 'message.sent';

        $data['user_id'] = $user['id'];
        $messageId = $this->messageRepository->saveMessage($data);

        $message = $this->messageRepository->formatMessage($messageId);

        // $conversation = DB::table('conversations')->where('id', $conversation_id)->first();
        $participants = $this->conversationRepository->conversationParticipants($data);

        foreach ($participants as $participant) {
            $channel = config('pusher.PRIVATE_CONVERSATION').$participant->user_id;
            $this->pusherServices->push(
                $event,
                $channel,
                $data['conversationId'], 
                $message, 
            );
        }

        $array[] = $message;
        return $array;
    }
    public function sendMessageAi(array $data): array
    {
        $array = [];        
        // 1. find user
        $user = $this->jwtServices->getContent();
        unset($user['exp']);
        $event = 'message.sent';
        $data['user_id'] = $user['id'];
        // 2. save user message
        $messageId = $this->messageRepository->saveMessage($data);
        $message = $this->messageRepository->formatMessage($messageId);
        $array[]=$message;

        // 3. send to AI api
        $aiText = $this->groqServices->send($data);

        // 4. cuvamo ai response kao poruku kod nas u bazi

        $aiData = [
            'conversationId' => $data['conversationId'],
            'isEncrypted' => 0,
            'text' => $aiText,
            'user_id' => 101
        ];
        $aiMessageId = $this->messageRepository->saveMessage($aiData);

        // $conversation = DB::table('conversations')->where('id', $conversation_id)->first();
        $aiData['user_id'] = 101; // Ai user
        $participants = $this->conversationRepository->conversationParticipants($aiData);

        $aiMessage = $this->messageRepository->formatMessage($aiMessageId);

        // Not sending for now, eventualy for gorup chat...
        // foreach ($participants as $participant) {
        //     $channel = config('pusher.PRIVATE_CONVERSATION').$participant->user_id;
        //     $this->pusherServices->push(
        //         $event,
        //         $channel,
        //         $aiData['conversationId'], 
        //         $aiMessage, 
        //     );
        // }
        $array[]=$aiMessage;
        return $array;
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