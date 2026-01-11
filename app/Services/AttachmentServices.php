<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Attachment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use stdClass;


class AttachmentServices {

    private JWTServices $jwtServices;
    private PusherServices $pusherServices;
    public function __construct(JWTServices $jwtServices, PusherServices $pusherServices) {
        $this->pusherServices = $pusherServices;
        $this->jwtServices = $jwtServices;
    }

    public function sendAttachment(array $data): stdClass
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];
        $event = 'message.sent';
        $attachment_path = config('app.url') . '/storage/';

        $file = $data['file'];
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();

        $path = $file->store('attachments', 'public'); 

        // Kreiranje poruke tipa attachment
        $message_id = DB::table('messages')->insertGetId([
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $user_id,
            'type' => 'attachment',
            'message' => null, // jer je attachment
        ]);
        $attachmentData = [
            'message_id' => $message_id,
            'type' => Str::startsWith($mimeType, 'image') ? 'image' : 'video',
            'path' => $path,
            'size' => $file->getSize(),
        ];
        $attachment = Attachment::create($attachmentData);

        $message = DB::table('messages as m')
        ->join('users as u', 'u.id', 'm.sender_id')
        ->leftJoin('attachments as a', function($join) {
            $join->on('a.message_id', '=', 'm.id')
                ->where('m.type', '=', 'attachment');
        })
        ->select(
            'm.*', 
            'u.name as sender_name', 
            DB::raw("CONCAT('" . config('app.url') . "/images/avatar/', u.avatar) as avatar_url"),
                        DB::raw("CONCAT('" . $attachment_path . "', a.path) as attachment_path"),
        )
        ->where('m.id', $message_id)
        ->first();

        // $conversation = DB::table('conversations')->where('id', $conversation_id)->first();
        $participants = DB::table('conversation_user')
        ->select('user_id')
        ->where('conversation_id', $message->conversation_id)
        ->where('user_id', '!=', $user['id'])
        ->get();

        foreach ($participants as $participant) {
            $channel = config('pusher.PRIVATE_CONVERSATION').$participant->user_id;
            $this->pusherServices->push(
                $event,
                $channel,
                $data['conversation_id'], 
                $message, 
            );
        }


        return $message;
    }

    private function attachmentUpload(int $user_id, array $data) {
        
        $file = $data['file'];
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();

        $path = $file->store('attachments', 'public'); 

        // Kreiranje poruke tipa attachment
        $message = Message::create([
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $user_id,
            'type' => 'attachment',
            'message' => null, // jer je attachment
        ]);

        
        // Metadata
        $attachmentData = [
            'message_id' => $message->id,
            'type' => Str::startsWith($mimeType, 'image') ? 'image' : 'video',
            'path' => $path,
            'size' => $file->getSize(),
        ];
        $attachment = Attachment::create($attachmentData);

        return $attachment;
    }

}