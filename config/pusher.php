<?php

return [
    'app_id' => env('PUSHER_APP_ID', '1821016'),
    'key' => env('PUSHER_APP_KEY', 'd842d9bd852a8bbc74b0'),
    'secret' => env('PUSHER_APP_SECRET', '19954d590e875e506b86'),
    'cluster' => env('PUSHER_APP_CLUSTER', 'eu'),
    'useTLS' => true,

    'PRIVATE_CONVERSATION' => env('PRIVATE_CONVERSATION', 'private-user-'),
    'MESSAGE_SENT' => env('MESSAGE_SENT', 'message.sent'),
    'MESSAGE_SEEN' => env('MESSAGE_SEEN', 'message.seen'),
];
