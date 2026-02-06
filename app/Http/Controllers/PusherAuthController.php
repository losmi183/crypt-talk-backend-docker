<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use Illuminate\Http\Request;
use App\Services\JWTServices;
use Illuminate\Support\Facades\Log;

class PusherAuthController extends Controller
{
    private JWTServices $jWTServices;
    public function __construct(JWTServices $jWTServices) {
        $this->jWTServices = $jWTServices;
    }

    public function authenticate(Request $request)
    {
        // JWT middleware je već prošao i garantuje validnog usera
        $user = $this->jWTServices->getContent();
        $userId = (int) $user['id'];

        $channelName = $request->input('channel_name');
        $socketId = $request->input('socket_id');

        // Očekivani format: private-user-{userId}
        if (!preg_match('/^private-user-(\d+)$/', $channelName, $matches)) {
            return response('Forbidden', 403);
        }

        $channelUserId = (int) $matches[1];

        // User može da se subscribe-uje samo na SOPSTVENI kanal
        if ($channelUserId !== $userId) {
            return response('Forbidden', 403);
        }

        // $pusher = new Pusher(
        //     'd842d9bd852a8bbc74b0',
        //     '19954d590e875e506b86',
        //     '1821016',
        //     [
        //         'cluster' => 'eu',
        //         'useTLS'  => true,
        //     ]
        // );
        $pusher = new Pusher(
            config('pusher.key'),
            config('pusher.secret'),
            config('pusher.app_id'),
            [
                'cluster' => config('pusher.cluster'),
                'useTLS'  => config('pusher.useTLS', true),
            ]
        );

        // Autorizacija uspešna
        return response(
            $pusher->socket_auth($channelName, $socketId)
        );
    }
}