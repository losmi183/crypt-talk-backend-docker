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
        DB::table('conversations')->insert([
            [
                'id' => 1,
                'type' => 'private',
                'salt' => '8f9c180a5e48746fb8ab4bd196ad4e4b'
            ],
            [
                'id' => 2,
                'type' => 'private',
                'salt' => '8f9c180a5e48746fb8ab4bd196ad4e4b'
            ],
            [
                'id' => 3,
                'type' => 'group',
                'salt' => '8f9c180a5e48746fb8ab4bd196ad4e4b'
            ],
            [
                'id' => 5,
                'type' => 'private',
                'salt' => '8f9c180a5e48746fb8ab4bd196ad4e4b'
            ],
        ]);
        DB::table('conversations')->insert([
            'id' => 4,
            'type' => 'group',
            'salt' => '8f9c180a5e48746fb8ab4bd196ad4e4b',
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
