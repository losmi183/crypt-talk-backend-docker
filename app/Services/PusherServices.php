<?php

namespace App\Services;

use Pusher\Pusher;
use Illuminate\Support\Facades\Log;

class PusherServices {
    public function __construct() {

    }

    public function push(string $event, string $channel, int $conversation_id, string $content, array $user) : bool 
    {
        
        $pusher = new Pusher(
            config('pusher.key'),
            config('pusher.secret'),
            config('pusher.app_id'),
            [
                'cluster' => config('pusher.cluster'),
                'useTLS' => config('pusher.useTLS', true),
            ]
        );
        
        Log::info('$recipient_id');
        Log::info( $conversation_id);

        Log::info('$pusher');
        Log::info(json_encode($pusher));
        // Log::info(config('pusher.secret'));
        // Log::info(config('pusher.app_id'));
        // Log::info(config('pusher.cluster'));
        // Log::info(config('pusher.useTLS'));
        

        // Privatni kanal za korisnika recipient_id
        $pusher->trigger($channel, $event, [
            'message' => $content,
            'from' => $user,
        ]);

        return true;
    }
}