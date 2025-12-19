<?php

namespace App\Repository;

use App\Models\User;
use App\Models\Message;
use App\Models\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository
{
   public function save(array $data): Message
   {
       return Message::create($data);
   }
}