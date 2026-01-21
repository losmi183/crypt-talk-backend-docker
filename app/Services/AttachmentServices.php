<?php

namespace App\Services;

use App\Repository\MessageRepository;
use stdClass;
use App\Models\Attachment;
use Illuminate\Support\Str;
use App\Services\MediaServices;
use Illuminate\Support\Facades\DB;

class AttachmentServices {

    private JWTServices $jwtServices;
    private PusherServices $pusherServices;
    private MediaServices $mediaServices;
    private MessageRepository $messageRepository;
    public function __construct(JWTServices $jwtServices, PusherServices $pusherServices, MediaServices $mediaServices, MessageRepository $messageRepository) {
        $this->pusherServices = $pusherServices;
        $this->jwtServices = $jwtServices;
        $this->mediaServices = $mediaServices;
        $this->messageRepository = $messageRepository;
    }

    public function sendAttachment(array $data): stdClass
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];
        $event = 'message.sent';
        // $attachment_path = config('app.url') . '/storage/';
        

        $file = $data['file'];
        $originalName = $file->getClientOriginalName();
        // Ako je ekstenzija .webm ili .oga, forsiraj audio type
        if (in_array($file->getClientOriginalExtension(), ['webm', 'oga', 'ogg'])) {
            $mimeType = 'audio/webm'; // Forsiraj audio
        } else {
            $mimeType = $file->getMimeType();
        }
        $duration = $this->mediaServices->getDuration($file, $mimeType);
        $fileType = $this->mediaServices->getFileType($mimeType);
        
        $path = $file->store($fileType, 'private');

        // Create thumbnail
        if($fileType === 'image') {
            $thumbnailPath = $this->mediaServices->makePhotoThumbnail($file, $path, $mimeType);
        }
        if($fileType === 'video') {
             $thumbnailPath = $this->mediaServices->makeVideoThumbnail($path);
        }   

        // Create message and attachment
        $message_id = DB::table('messages')->insertGetId([
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $user_id,
            'type' => 'attachment',
            'message' => null, // jer je attachment
        ]);
        DB::table('attachments')->insert([
            'message_id' => $message_id,
            'type' => $fileType,
            'path' => $path,
            'thumbnail' => $thumbnailPath ?? null, 
            'size' => $file->getSize(),
            'duration' => $duration
        ]);

        $message = $this->messageRepository->getMessage($message_id);

        // $conversation = DB::table('conversations')->where('id', $conversation_id)->first();
        $participants = $this->messageRepository->getParticipants($message->conversation_id, $user['id']);

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
}