<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    public function users() {
        return $this->belongsToMany(User::class, 'conversation_user')
                    ->withPivot('last_read_message_id')
                    ->withTimestamps();
    }

    public function scopeForUser($query, $user_id)
    {
        return $query
            ->whereHas('users', function($q) use ($user_id) {
                $q->where('users.id', $user_id);
            })
            ->with([
                'users' => function($q) use ($user_id) {
                    $q->where('users.id', '!=', $user_id)
                    ->select('users.id', 'users.name', 'users.avatar', 'conversation_user.last_read_message_id');
                }
            ]);
    }

}
