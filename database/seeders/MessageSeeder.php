<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Početno vreme: 1 sat unazad
        $timestamp = Carbon::now()->subHour();

        $messages = [
            'U2FsdGVkX19iHIp/3U3QsDkh/aL2SVXaf7xYhSpMSgU=',
            'U2FsdGVkX1/ceey+nurZLHdJC9/Kv0C92uGGeA8aPlU=',
            'U2FsdGVkX18DUlP60W4Ya0m8ZptstIJMsKU17iYyNa4=',
            'U2FsdGVkX1/EvKlNXJpin2APhiazST5AxgBHpSFGlvw=',
            'U2FsdGVkX1/TiGro9g+0gJ8LJTzW3qU95Xi/yNDqo935GMb4RCyZPmoRDvFq8WlS',
            'U2FsdGVkX1/zyzHgVk786fXz2oh3jUn+nyvIgCqPCpY=',
            'U2FsdGVkX1/fN0f4RhL7IHs/m6Gow8BmY21Wev1Klqk=',
            'U2FsdGVkX1+M7KpAWKITE1oothclg8Jvh5qnoQ4AWLw=',
            'U2FsdGVkX18FPplL2vLRsbttGO88xPZLelRe8leyD08=',
            'U2FsdGVkX1/S2wRAQD4GsqcmY3qZUhN+2B6j9XY96wM=',
            'U2FsdGVkX1+eU3b1P7RzaveA/zWlqDIAHJN9/YREIus=',
            'U2FsdGVkX1/PALPTHXmFkkhJhFh+L8BJlwxNkaNp2w8=',
            'U2FsdGVkX1/HkNHWjyVjvfStD1Kgyvv6k34taqnR888=',
            'U2FsdGVkX199ayOJPjzxdSpC9e0+Y3K95VJRmdMqXeCee3Y9ytta+G66OrytfHNz',
            'U2FsdGVkX18ouRk5J0alF5G3EQNk25dSEX/17DtmsPU=',
            'U2FsdGVkX1/5vtfqhvznFU8WG/qA1EEydI/zgtI4uRQ=',
            'U2FsdGVkX19mSjCGsFWO86MvjKX36V7sVVWTBXngHilDNAPn4bHuxI49mtZ8wS5J',
            'U2FsdGVkX1/ZODl8dAGkRm7Z4VWB/kKbVsw56xAeJs4=',
            'U2FsdGVkX1+IpZLy5K1JCntusnofQOUuDptF/wY0McQ=',
            'U2FsdGVkX1/dpxKRjPIzROg0MAaifQwRlUZq75SmK5c=',
            'U2FsdGVkX1+caDUvlgRQ05RMRZf6aeS5SK4e7hxF59Q=',
            'U2FsdGVkX1+THLy9nCBBSKRz7+gByhbSnmO6HywTDr4=',
            'U2FsdGVkX1/UAEGqqUs+n5jcqFqtMriMZy1Uc32jusPwHjflzd8jXN0RhWfpkf0i',
            'U2FsdGVkX190xghUh3b+StX4/BSLFY8j82w68t9fh90=',
            'U2FsdGVkX1/oXdntbmvxMOkW/SJLdfLeAfthLLJQGYs=',
            'U2FsdGVkX1/1NC9gSduRh87/m0oNbQvnJqb/UR0UvtU=',
            'U2FsdGVkX1/H+CJ9V1EnrrCzlQ2jeHMtrPJNr3mxg5E=',
            'U2FsdGVkX1+ut8cBqADKmSDPbJ22M4BXZY3irZ3uZ50=',
            'U2FsdGVkX19d1IjjvaCEJIocWR1XVGT89Kec++lhZlE=',
        ];

        // Ubacujemo po dve poruke po iteraciji
        // for ($i = 0; $i < count($messages); $i += 2) {
        //     // user 1 → user 2
        //     DB::table('messages')->insert([
        //         'conversation_id' => 1,
        //         'sender_id' => 1,
        //         'message' => $messages[$i],
        //         'created_at' => $timestamp->copy(),
        //         'updated_at' => $timestamp->copy(),
        //     ]);
        //     $timestamp->addMinutes(1);
        //     // user 2 → user 1
        //     if (isset($messages[$i + 1])) {
        //         DB::table('messages')->insert([
        //             'conversation_id' => 1,
        //             'sender_id' => 2,
        //             'message' => $messages[$i + 1],
        //             'created_at' => $timestamp->copy(),
        //             'updated_at' => $timestamp->copy(),
        //         ]);
        //     }            
        //     $timestamp->addMinutes(1);
        // }

        // for ($i = 0; $i < count($messages); $i += 2) {
        //     // user 1 → user 2
        //     DB::table('messages')->insert([
        //         'conversation_id' => 2,
        //         'sender_id' => 1,
        //         'message' => $messages[$i],
        //         'created_at' => $timestamp->copy(),
        //         'updated_at' => $timestamp->copy(),
        //     ]);
        //     $timestamp->addMinutes(1);
        //     // user 2 → user 1
        //     if (isset($messages[$i + 1])) {
        //         DB::table('messages')->insert([
        //             'conversation_id' => 2,
        //             'sender_id' => 2,
        //             'message' => $messages[$i + 1],
        //             'created_at' => $timestamp->copy(),
        //             'updated_at' => $timestamp->copy(),
        //         ]);
        //     }            
        //     $timestamp->addMinutes(1);
        // }

        // for ($i = 0; $i < count($messages); $i += 2) {
        //     // user 1 → user 2
        //     DB::table('messages')->insert([
        //         'conversation_id' => 3,
        //         'sender_id' => 1,
        //         'message' => $messages[$i],
        //         'created_at' => $timestamp->copy(),
        //         'updated_at' => $timestamp->copy(),
        //     ]);
        //     $timestamp->addMinutes(1);
        //     // user 2 → user 1
        //     if (isset($messages[$i + 1])) {
        //         DB::table('messages')->insert([
        //             'conversation_id' => 3,
        //             'sender_id' => 2,
        //             'message' => $messages[$i + 1],
        //             'created_at' => $timestamp->copy(),
        //             'updated_at' => $timestamp->copy(),
        //         ]);
        //     }            
        //     if (isset($messages[$i + 2])) {
        //         DB::table('messages')->insert([
        //             'conversation_id' => 3,
        //             'sender_id' => 1001,
        //             'message' => $messages[$i + 1],
        //             'created_at' => $timestamp->copy(),
        //             'updated_at' => $timestamp->copy(),
        //         ]);
        //     }            
        //     $timestamp->addMinutes(1);
        // }
    }
}
