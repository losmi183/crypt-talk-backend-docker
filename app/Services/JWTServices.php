<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\UnexpectedValueException;
use Firebase\JWT\SignatureInvalidException;

class JWTServices
{
    private $content = null;
    private $key = null;

    public function __construct()
    {
        $key = env('APP_KEY', "base64:pNRM1hxbl2F78ocAnr+ybyiJ/Wor3HznH+Fb+I5KxEo=");
        // $key = env('APP_KEY'); // Nekad neće da čita iz .env pa je dodato default
        $key = (string) $key;
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        $this->key = $key;
    }

    /**
     * @param array $data
     * @param int|null $ttl=null
     * 
     * @return \stdClass
     */
    public function setPair(array $data, ?int $ttl=null): \stdClass
    {
        $obj = new \stdClass;

        if($ttl) {
            $jwt_time_to_live = $ttl;
        } else {
            $jwt_time_to_live = config('settings.JWT2LIVEMIN');
        }
        $obj->token = $this->createJWT($data, $jwt_time_to_live);
        $jwt_refresh_t2l = config('settings.JWT2RFSHMIN');
        $obj->rtoken = $this->createJWT($data, $jwt_refresh_t2l);

        return $obj;
    }

    /**
     * @param string $token
     * 
     * @return int
     */
    public function decodeJWT(string $token): int
    {
        $this->content = null;
        try{
            //Algoritam je odredjen ovde pa bi trebalo da zastiti od laznih tokena bez algoritma
            $this->content = (array)JWT::decode($token, new Key($this->key, 'HS256'));
            return 200;
        } catch (SignatureInvalidException $e) {
            return 401;
        } catch (ExpiredException $e) {
            return 403;
        } catch (\Exception $e) {
            return 406;
        }
    }

    /**
     * @param User $user
     * @param float $ttl_minutes
     * 
     * @return string
     */
    public function createJWT(User $user, float $ttl_minutes): string
    {   
        $userData = [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'about' => $user->about,
            'role' => $user->role,
            'avatar' => $user->avatar,
        ];
        $userData['exp'] = time() + $ttl_minutes * 60;
        $token = JWT::encode($userData, $this->key, 'HS256');
        return $token;
    }

    /**
     * @param array $user
     * @param float $ttl_minutes
     * 
     * @return string
     */
    public function encrypt(array $user, float $ttl_minutes): string
    {   

        $userData = [
            'exp' => time() + $ttl_minutes * 60,
            'email' => $user['email'],
            // 'name' => $user['name'],
        ];

        $userData['exp'] = time() + $ttl_minutes * 60;
        $token = JWT::encode($userData, $this->key, 'HS256');
        return $token;
    }



    /**
     * @return array|null
     */
    public function getContent(): ?array
    {
        return $this->content;
    }
}
