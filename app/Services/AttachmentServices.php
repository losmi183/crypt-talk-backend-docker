<?php

namespace App\Services;

use stdClass;
use App\Models\Message;
use App\Models\Attachment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
        
        $folder = Str::startsWith($mimeType, 'image') ? 'images' : (Str::startsWith($mimeType, 'video') ? 'videos' : 'attachments');$folder = Str::startsWith($mimeType, 'image') ? 'images' 
        : (Str::startsWith($mimeType, 'video') ? 'videos' 
        : (Str::startsWith($mimeType, 'audio') ? 'audio' 
        : 'attachments'));

        $path = $file->store($folder, 'private');

        if (Str::startsWith($mimeType, 'image')) {
            $thumbnailPath = $this->makePhotoThumbnail($file, $path, $mimeType);
        }
        if (Str::startsWith($mimeType, 'video')) {
            $thumbnailPath = $this->makeVideoThumbnail($path);
        }


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
            'thumbnail' => $thumbnailPath, 
            'size' => $file->getSize(),
        ];
        $attachment = Attachment::create($attachmentData);

        // 3. Kreiranje thumbnail-a ako je slika

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

 
    private function makePhotoThumbnail($file, $originalPath, $mimeType): string|null
    {
        // create image manager with desired driver
        $manager = new ImageManager(new Driver());

        // read image from file system
        $image = $manager->read($file->getRealPath());

        // resize image proportionally to 300px width
        $image->scale(height: 200);

        // Kreiraj ime fajla za thumbnail
        $filename = pathinfo($originalPath, PATHINFO_FILENAME) . '_thumb.' . $file->getClientOriginalExtension();

        // Putanja unutar storage/app/public
        $thumbnailPath = 'thumbnails/' . $filename;

        // Snimi thumbnail u storage disk 'public'
        Storage::disk('private')->put($thumbnailPath, (string) $image->encode());

        // Vrati putanju thumbnail-a
        return $thumbnailPath;
    }   

    private function makeVideoThumbnail(string $relativeVideoPath, string $extension = 'jpg'): ?string
    {
        // apsolutna putanja do video fajla
        $absoluteVideoPath = storage_path('app/' . $relativeVideoPath);

        if (!file_exists($absoluteVideoPath)) {
            return null;
        }

        // thumbnails folder: storage/app/thumbnails
        $thumbnailDir = storage_path('app/thumbnails');

        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }

        // ime thumbnail-a: koristi ime originalnog fajla
        $filename = pathinfo($relativeVideoPath, PATHINFO_FILENAME) . '_thumb.' . $extension;

        // relativna putanja (za bazu)
        $thumbnailPath = 'thumbnails/' . $filename;

        // apsolutna putanja za ffmpeg
        $absoluteThumbnailPath = storage_path('app/' . $thumbnailPath);

        // uzmi frame na 1 sekundi (-ss 1)
        $cmd = sprintf(
            'ffmpeg -y -i %s -ss 00:00:01 -vframes 1 %s 2>/dev/null',
            escapeshellarg($absoluteVideoPath),
            escapeshellarg($absoluteThumbnailPath)
        );

        exec($cmd, $output, $exitCode);

        // proveri da li je FFmpeg uspeo
        if ($exitCode !== 0 || !file_exists($absoluteThumbnailPath)) {
            return null;
        }

        return $thumbnailPath;
    }

    public function show(string $token, string  $type, string  $file)
    {
        // Ograniči pristup samo folderu images
        // $filePath = storage_path('app/' . $type . '/' . $file);

        // if (!file_exists(filename: $filePath)) {
        //     abort(404, 'File not found');
        // }

        // return $filePath;
    }


}