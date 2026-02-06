<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $iterations = config('crypto.pbkdf2_iterations');

        DB::table('conversations')->insert([
            [
                'id' => 1,
                'type' => 'private',
                'salt' => '8f9c180a5e48746fb8ab4bd196ad4e4b', // različit hex → bin
                'iterations' => $iterations,
                'title' => null,
            ],
            [
                'id' => 2,
                'type' => 'private',
                'salt' => '2a1f6c8d9bfa34712c8e4d0f1b2a3c4d',
                'iterations' => $iterations,
                'title' => null,
            ],
            [
                'id' => 3,
                'type' => 'group',
                'salt' => '5d6a1c8b9f2e4a1b3c4d5e6f7a8b9c0d',
                'iterations' => $iterations,
                'title' => null,
            ],
        ]);



        DB::table('conversation_user')->insert([
            [
                'conversation_id' => 1,
                'user_id' =>1,
                'joined_at' => now()
            ],
            [
                'conversation_id' => 1,
                'user_id' =>2,
                'joined_at' => now()
            ],


            [
                'conversation_id' => 2,
                'user_id' =>1,
                'joined_at' => now()
            ],
            [
                'conversation_id' => 2,
                'user_id' =>1001,
                'joined_at' => now()
            ],


            [
                'conversation_id' => 3,
                'user_id' =>1,
                'joined_at' => now()
            ],
            [
                'conversation_id' => 3,
                'user_id' =>1001,
                'joined_at' => now()
            ],
            [
                'conversation_id' => 3,
                'user_id' =>2,
                'joined_at' => now()
            ],     
        ]);

        $users = DB::table('users')->where('id', '!=', 101)->get();
        foreach ($users as $user) {
            $conversation_id = DB::table('conversations')->insertGetId([
                'type' => 'chatbot',
                'title' => 'Ai assistent',
            ]);
            DB::table('conversation_user')->insert([
                'conversation_id' => $conversation_id,
                'user_id' => 101,
            ]);
            DB::table('conversation_user')->insert([
                'conversation_id' => $conversation_id,
                'user_id' => $user->id,
            ]);
            // DB::table('messages')->insert([
            //     'conversation_id' => $conversation_id,
            //     'sender_id' => 101,
            //     'type' => 'message',
            //     'message' => "Hello, I'm your Ai assistent. How I can help you? You can ask me anything about crypt message app"
            // ]);
        }
    }
}
