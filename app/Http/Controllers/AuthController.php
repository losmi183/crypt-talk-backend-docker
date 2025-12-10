<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\AuthServices;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\GoogleLoginRequest;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public AuthServices $authServices;
    public function __construct(AuthServices $authServices) {
        $this->authServices = $authServices;
    }

    #[OA\Post(
        path: '/auth/register',
        summary: 'Register new user',
        requestBody: new OA\RequestBody(required: true,
        content: new OA\MediaType(mediaType: 'application/json',
        schema: new OA\Schema(required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'name', type: 'string', default: 'newuser', description: 'user name'),
                new OA\Property(property: 'email', type: 'string', default: 'newuser@mail.com', description: 'email'),
                new OA\Property(property: 'password', type: 'string', default: 'Secret123#', description: 'password'),
            ]
        ),
    )),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'User registered'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error')
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->authServices->register($data);

        return response()->json([
            'message' => 'Please check your email to finish registration',
            'data' => $data
        ]);
    }    

    #[OA\Post(
        path: '/auth/resend-verify-email',
        summary: 'resend verify email',
        requestBody: new OA\RequestBody(required: true,
        content: new OA\MediaType(mediaType: 'application/json',
        schema: new OA\Schema(required: ['email'],
            properties: [
                new OA\Property(property: 'email', type: 'string', default: 'resend@mail.com', description: 'email'),
            ]
        ),
    )),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Verify email sent'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error')
        ]
    )]
    public function resendVerifyEmail(Request $request) {

        $email = $request->email;
        
        $result = $this->authServices->resendVerifyEmail($email);

        return response()->json($result);
    }

    #[OA\Get(
        path: '/auth/verify-email',
        summary: 'Verify email address',
        tags: ['Auth'],
        parameters: [
            new OA\Parameter(
                name: 'verify_token',
                in: 'query',
                required: true,
                description: 'Email verification token from email link',
                schema: new OA\Schema(type: 'string'),
                example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_FOUND, 
                description: 'Redirects to frontend login page'
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST, 
                description: 'Invalid or expired token'
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND, 
                description: 'User not found'
            )
        ]
    )]
    public function verifyEmail(Request $request) {

        $verify_token =  $request->query('verify_token');
        
        $result = $this->authServices->verifyEmail($verify_token);

        $frontendUrl = env('APP_ENV') === 'production' 
            ? env('FRONTEND_PROD') 
            : env('FRONTEND_DEV');

        $loginUrl = $frontendUrl . 'login';

        return redirect($loginUrl);
    }

    #[OA\Post(
        path: '/auth/login',
        summary: 'Login user',
        requestBody: new OA\RequestBody(required: true,
        content: new OA\MediaType(mediaType: 'application/json',
        schema: new OA\Schema(required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', default: 'milos@mail.com', description: 'email'),
                new OA\Property(property: 'password', type: 'string', default: 'milos', description: 'password'),
            ]
        ),
    )),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Korisnik prijavljen'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error')
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->authServices->login($data);

        return response()->json([
            'token' => $result
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $result = $this->authServices->refresh();

        return response()->json($result);
    }

    public function googleLogin(GoogleLoginRequest $request)
    {
        $data = $request->validated();

        $result = $this->authServices->googleLogin($data['idToken']);

        return response()->json([
            'token' => $result
        ]);
    }

    public function handleGoogleCallback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return redirect('/login')->withErrors(['Google login failed']);
        }

        // AuthServices koristi code da dobije token i podatke od Google
        $token = $this->authServices->handleGoogleOAuthCode($code);

        // Možeš da preusmeriš front sa JWT tokenom u query string ili cookie
        return redirect("https://crypt-talk.online/login?token={$token}");
    }

    #[OA\Get(
        path: "/auth/whoami",
        summary: "Podaci o korisniku na osnovu tokena",
        tags: ["Auth"],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: "Podaci o korisniku vraćeni na front"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function whoami(Request $request): JsonResponse
    {
        $user = $this->authServices->whoami();
        $userData = User::find($user['id']);
        $userData->avatar = config('settings.avatar_path') . $userData->avatar;
        return response()->json($userData);
    }
    
    #[OA\Post(
        path: "/auth/logout",
        summary: "Odjava sa sistema",
        tags: ["Auth"],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: "Korisnik odjavljen iz sistema"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function logout(): JsonResponse
    {
        return response()->json([
            'message' => 'Logout successfully'
        ]);
    }
}
