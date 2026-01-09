<?php

namespace App\Services;

use Pusher\Pusher;
use Illuminate\Support\Facades\Log;
use stdClass;

class PusherServices {
    public function __construct() {

    }

    public function push(string $event, string $channel, int $conversation_id, stdClass $message) : bool 
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

        // Privatni kanal za korisnika recipient_id
        $pusher->trigger($channel, $event, [
            'message' => $message,
        ]);

        return true;
    }
}