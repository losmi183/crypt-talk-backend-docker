<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    
    /**
     * The attributes that are mass assignable.
    *
    * @var list<string>
    */
    protected $guarded = ['id'];
    
    /**
     * The attributes that should be hidden for serialization.
    *
    * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    /**
     * Get the attributes that should be cast.
    *
    * @return array<string, string>
    */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['avatar_url'];
    public function getAvatarUrlAttribute() 
    {
        $path = config('app.url') . '/images/avatar/';

        if(!$this->avatar) {
            return $path . 'default.png';
        }
        return $path . $this->avatar;
    }
}
