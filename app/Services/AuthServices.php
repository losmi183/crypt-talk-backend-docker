<?php

namespace App\Services;
use stdClass;
use Throwable;
use Google_Client;
use App\Models\User;
use GuzzleHttp\Client;
use App\Mail\VerifyEmail;
use App\Mail\ForgotPasswordEmail;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

        return redirect()->away(env('FRONTEND_DEV') . 'login?status=email_verified');     
    }

    /**
     * @param array $data
     * 
     * @return string
     */
    public function login(array $data): stdClass
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if(!$user) {
            abort(400, 'User not found');
        }

        if(!$user->active_from) {
            abort(400, 'Account not active');
        }

        // If user found, check password is correct
        if ($user && Hash::check($data['password'], $user->password)) {
            $tokens = $this->jwtServices->setPair($user, 60000);
            return $tokens;
        }

        abort(400, 'Invalid credentials');
    }

    public function refresh(string $refresh_token): \stdClass
    {
        $status = $this->jwtServices->decodeJWT($refresh_token);

        if ($status == 403) {
            abort( 401, 'Refresh token has expired');
        }
        if ($status != 200) {
            abort(400, 'Token not valid');
        }

        $userData = $this->jwtServices->getContent();

        $user = User::where('email', $userData['email'])
        ->where('active_from', '!=', null)
        ->first();

        if(!$user) {
            abort(404, 'User not exists or suspended!');
        }

        return $this->jwtServices->setPair($user, 60);
    }

    public function forgotPassword(array $data): bool
    {
        $user = User::where('email', $data['email'])->where('active_from', '!=', null)->first();

        if(! $user) {
            abort(404 , 'User not found or not active');
        }

        $forgotPasswordToken = $this->jwtServices->createJWT($user, config('jwt.JWT2LIVEFORGOTPASS') );

        // 3. Send email
        try {
            Mail::to($user->email)->send(
                new ForgotPasswordEmail($user, $forgotPasswordToken)
            );
        } catch(Throwable $ex) {
            Log::error($ex->getMessage());
        }

        return true;
    }

    public function resetPassword(array $data)
    {
        $status = $this->jwtServices->decodeJWT($data['forgot_password_token']);
        if ($status == 403) {
            abort( 403, 'Token has expired');
        }
        if ($status != 200) {
            abort(400, 'Token not valid');
        }

        $userData = $this->jwtServices->getContent(); 

        try {
            \DB::table('users')->where('email', $userData['email'])->update(['password' => bcrypt($data['password'])]);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            abort(400, 'Password not reset');
        }
    }

    // public function googleLogin(?string $idToken): string
    // {
    //     $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID_WEB')]);
    //     $payload = $client->verifyIdToken($idToken);

    //     if (!$payload) return response()->json(['error' => 'Invalid token'], 401);

    //     $googleId = $payload['sub'];
    //     $email = $payload['email'];
    //     $name = $payload['name'] ?? '';

    //     $user = User::firstOrCreate(
    //         ['email' => $email],
    //         [
    //             'name' => $name,
    //             'password' => bcrypt(str()->random(32)),
    //             'google_id' => $googleId
    //         ]
    //     );

    //     $token = $this->jwtServices->createJWT($user, 60000);

    //     return $token;
    // }

    // public function handleGoogleOAuthCode(string $code): string
    // {
    //     // izaberi kredencijale u zavisnosti od okruženja
    //     if (app()->environment('local')) {
    //         $clientId = env('GOOGLE_CLIENT_ID_LOCAL');
    //         $clientSecret = env('GOOGLE_CLIENT_SECRET_LOCAL');
    //         $redirect = env('GOOGLE_REDIRECT_LOCAL');
    //     } else {
    //         $clientId = env('GOOGLE_CLIENT_ID_WEB');
    //         $clientSecret = env('GOOGLE_CLIENT_SECRET_WEB');
    //         $redirect = env('GOOGLE_REDIRECT_WEB');
    //     }

    //     // 1. Zamena code → token
    //     $client = new Client();

    //     $response = $client->post('https://oauth2.googleapis.com/token', [
    //         'form_params' => [
    //             'code' => $code,
    //             'client_id' => $clientId,
    //             'client_secret' => $clientSecret,
    //             'redirect_uri' => $redirect,
    //             'grant_type' => 'authorization_code',
    //         ],
    //     ]);

    //     $data = json_decode($response->getBody(), true);

    //     if (!isset($data['id_token'])) {
    //         abort(401, 'Google did not return id_token');
    //     }

    //     $idToken = $data['id_token'];

    //     // 2. Validacija ID tokena
    //     $googleClient = new \Google_Client(['client_id' => $clientId]);
    //     $payload = $googleClient->verifyIdToken($idToken);

    //     if (!$payload) {
    //         abort(401, 'Invalid Google token');
    //     }

    //     // 3. Upis ili kreiranje korisnika
    //     $email = $payload['email'];
    //     $name = $payload['name'] ?? '';
    //     $googleId = $payload['sub'];

    //     $user = User::firstOrCreate(
    //         ['email' => $email],
    //         [
    //             'name' => $name,
    //             'google_id' => $googleId,
    //             'password' => bcrypt(str()->random(32)),
    //         ]
    //     );

    //     // 4. Generiši JWT i vrati
    //     return $this->jwtServices->createJWT($user, 60000);
    // }

    public function whoami(): ?array
    {
        return $this->jwtServices->getContent();
    }

    public function profileUpdate(array $data): User
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];

        // 1. Provera da li postoji avatar fajl
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile && $data['avatar']->isValid()) {
            // napravi folder ako ne postoji
            $destination = public_path('images/avatar');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $extension = $data['avatar']->getClientOriginalExtension();
            $filename = $user_id . '.' . $extension;

            // snimi u /public/images/avatar
            $data['avatar']->move($destination, $filename);

            // sačuvaj relativnu putanju (da lako praviš URL)
            $data['avatar'] = $filename;
        } else {
            unset($data['avatar']);
        }

        // update user-a
        // $data['avatar'] = $filename;
        try {
            $user = User::find($user_id);
            $user->update($data);
            $user->save();
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            abort(400, 'Profile update error');
        }
        return $user;
    }

    public function passwordUpdate(array $data): bool
    {
        $user = $this->jwtServices->getContent();

        $user = User::where('email', $user['email'])->first();

        try {
            $user->password = bcrypt($data['password']);
            $user->save();
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            abort(400, 'Password update error');
        }

        return true;
    }
}