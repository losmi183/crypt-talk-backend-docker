<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('landing-page');
});

// Route::get('/', function () {
//     // xdebug_info();
// phpinfo();
//     return 'pocetna';
// });

// Route::get('/test-mail', function () {
//     try {
//         \Mail::raw('Test email', function ($message) {
//             $message->to('test@example.com')
//                     ->subject('Test Email');
//         });   
//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => $e->getMessage(),
//             'trace' => $e->getTraceAsString()
//         ]);
//     }
// });