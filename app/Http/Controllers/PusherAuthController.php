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
        $pusher = new Pusher(
            config('pusher.key'),
            config('pusher.secret'),
            config('pusher.app_id'),
            [
                'cluster' => config('pusher.cluster'),
                'useTLS' => config('pusher.useTLS', true),
            ]
        );
        
        $channel_name = $request->input('channel_name');
        $socket_id = $request->input('socket_id');        
        
        // Izvuci conversation_id iz imena kanala
        preg_match('/private-conversation-(\d+)/', $channel_name, $m);
        $conversation_id = $m[1] ?? null;
        
        if (!$conversation_id) {
            return response('Forbidden', 403);
        }

        $user = $this->jWTServices->getContent();
        $user_id = $user['id'];

        // Provera da li user pripada toj konverzaciji
        $allowed = \DB::table('conversation_user')
            ->where('conversation_id', $conversation_id)
            ->where('user_id', $user_id)
            ->exists();
        
        if (!$allowed) {
            return response('Forbidden', 403);
        }
        
        // OK
        $auth = $pusher->socket_auth($channel_name, $socket_id);
        return response($auth);
    }
}