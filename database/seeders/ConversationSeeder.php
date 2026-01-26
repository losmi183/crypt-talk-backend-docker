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
            [
                'id' => 5,
                'type' => 'private',
                'salt' => '9f8e7d6c5b4a3928171625341a0b0c0d',
                'iterations' => $iterations,
                'title' => null,
            ],
        ]);

        DB::table('conversations')->insert([
            'id' => 4,
            'type' => 'group',
            'salt' => '9f8e7d6c5b4a3928171625341a0b0c0d',
            'iterations' => $iterations,
            'title' => 'nova godina'
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


            [
                'conversation_id' => 4,
                'user_id' =>1,
                'joined_at' => now()
            ],
            [
                'conversation_id' => 4,
                'user_id' =>3,
                'joined_at' => now()
            ],
            [
                'conversation_id' => 4,
                'user_id' =>2,
                'joined_at' => now()
            ],
            [
                'conversation_id' => 4,
                'user_id' =>4,
                'joined_at' => now()
            ],
            [
                'conversation_id' => 4,
                'user_id' =>5,
                'joined_at' => now()
            ],
            [
                'conversation_id' => 4,
                'user_id' =>6,
                'joined_at' => now()
            ],

            [
                'conversation_id' => 5,
                'user_id' =>2,
                'joined_at' => now()
            ],
            [
                'conversation_id' => 5,
                'user_id' =>1001,
                'joined_at' => now()
            ],

        ]);
    }
}
