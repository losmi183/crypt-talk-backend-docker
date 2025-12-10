<?php

namespace App\Services;
use Google_Client;
use App\Models\User;
use GuzzleHttp\Client;
use App\Mail\VerifyEmail;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AuthServices {

    private UserRepository $userRepository;
    private JWTServices $jwtServices;

    public function __construct(UserRepository $userRepository, JWTServices $jwtServices) {
        $this->userRepository = $userRepository;
        $this->jwtServices = $jwtServices;
    }

    /**
     * @param array $data
     * 
     * @return User
     */
    public function register(array $userData): User
    {   
        // 1. set token
        $registerToken = $this->jwtServices->encrypt($userData, 1440);  // 1440 - 24h

        // 3. Send email
        try {
            Mail::to($userData['email'])->send(
                new VerifyEmail($userData, $registerToken)
            );
        } catch(Throwable $ex) {
            Log::error($ex->getMessage());
        }

        $userData['password'] = Hash::make($userData['password']);

        return $this->userRepository->store($userData); 
    }

    /**
     * @param array $data
     * 
     * @return User
     */
    public function resendVerifyEmail(string $email): array
    {   
        // 0. get user 
        $user = User::where('email', $email)->first();
        if(!$user) {
            abort(400, 'Email not exsists! Go to registration.');
        }
        if($user['active_from'] !== null) {
            abort(400, 'Account already activated.');
        }

        // 1. set token
        $registerToken = $this->jwtServices->createJWT($user, 1440);  // 1440 - 24h


        $userData = [
            'name' => $user['name'],
            'email' => $user['email'],
        ];
        // 3. Send email
        try {
            Mail::to($email)->send(
                new VerifyEmail($userData, $registerToken)
            );
        } catch(Throwable $ex) {
            Log::error($ex->getMessage());
        }

        return $userData; 
    }

    public function verifyEmail($verify_token) {

        $status = $this->jwtServices->decodeJWT($verify_token);
        if ($status == 403) {
            abort( 403, 'Token has expired');
        }
        if ($status != 200) {
            abort(400, 'Token not valid');
        }

        $userData = $this->jwtServices->getContent(); 

        $email = $userData['email'];

        try {
            User::where('email', $email)
            ->update([
                'active_from' => now()
            ]);
        } catch(Throwable $ex) {
            Log::error($ex->getMessage());
            abort(400, 'Account not activated!');
        }

        $user = User::where('email', $email)->first();

        $token = $this->jwtServices->createJWT($user, 60000);
        return $token;
                
    }

    /**
     * @param array $data
     * 
     * @return string
     */
    public function login(array $data): string
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if(!$user->active_from) {
            abort(400, 'Account not active');
        }

        // If user found, check password is correct
        if ($user && Hash::check($data['password'], $user->password)) {
            $token = $this->jwtServices->createJWT($user, 60000);
            return $token;
        }

        abort(400, 'Invalid credentials');
    }

    public function refresh(): \stdClass
    {
        $user = $this->jwtServices->getContent();

        return $this->jwtServices->setPair($user, 60);
    }

    public function googleLogin(?string $idToken): string
    {
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID_WEB')]);
        $payload = $client->verifyIdToken($idToken);

        if (!$payload) return response()->json(['error' => 'Invalid token'], 401);

        $googleId = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'] ?? '';

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt(str()->random(32)),
                'google_id' => $googleId
            ]
        );

        $token = $this->jwtServices->createJWT($user, 60000);

        return $token;
    }

    public function handleGoogleOAuthCode(string $code): string
    {
        // izaberi kredencijale u zavisnosti od okruženja
        if (app()->environment('local')) {
            $clientId = env('GOOGLE_CLIENT_ID_LOCAL');
            $clientSecret = env('GOOGLE_CLIENT_SECRET_LOCAL');
            $redirect = env('GOOGLE_REDIRECT_LOCAL');
        } else {
            $clientId = env('GOOGLE_CLIENT_ID_WEB');
            $clientSecret = env('GOOGLE_CLIENT_SECRET_WEB');
            $redirect = env('GOOGLE_REDIRECT_WEB');
        }

        // 1. Zamena code → token
        $client = new Client();

        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirect,
                'grant_type' => 'authorization_code',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (!isset($data['id_token'])) {
            abort(401, 'Google did not return id_token');
        }

        $idToken = $data['id_token'];

        // 2. Validacija ID tokena
        $googleClient = new \Google_Client(['client_id' => $clientId]);
        $payload = $googleClient->verifyIdToken($idToken);

        if (!$payload) {
            abort(401, 'Invalid Google token');
        }

        // 3. Upis ili kreiranje korisnika
        $email = $payload['email'];
        $name = $payload['name'] ?? '';
        $googleId = $payload['sub'];

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'google_id' => $googleId,
                'password' => bcrypt(str()->random(32)),
            ]
        );

        // 4. Generiši JWT i vrati
        return $this->jwtServices->createJWT($user, 60000);
    }

    public function whoami(): ?array
    {
        return $this->jwtServices->getContent();
    }
}