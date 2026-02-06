<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\UserServices;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ConnectRequest;
use App\Http\Requests\UserSearchRequest;
use App\Http\Requests\UserUpdateServices;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ProfileUpdateServices;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private UserServices $userServices;
    public function __construct(UserServices $userServices) {
        $this->userServices = $userServices;
    }

    #[OA\Post(
        path: '/user/search',
        summary: 'Search user by name or email',
        requestBody: new OA\RequestBody(required: true,
        content: new OA\MediaType(mediaType: 'application/json',
        schema: new OA\Schema(required: ['search'],
        properties: [
            new OA\Property(property: 'search', type: 'string', default: 'user', description: 'search string'),
            ]
        ),
    )),
        tags: ['User'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'User registered'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error')
        ]
    )]
    public function search(UserSearchRequest $request, UserServices $userServices): JsonResponse
    {
        $data = $request->validated();
        $users = $userServices->search($data);
        return response()->json($users);
    }

    #[OA\Get(
        path: '/user/show/{id}',
        summary: 'Find user by ID',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'User ID',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'User found'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error')
        ]
    )]
    public function show($id): JsonResponse
    {
        $users = $this->userServices->show($id);
        return response()->json($users);
    }   

    #[OA\Get(
        path: '/user/edit',
        summary: 'Find user by auth token',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'User found'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error')
        ]
    )]
    public function edit(): JsonResponse
    {
        $user = $this->userServices->edit();
        return response()->json($user);
    }   

   #[OA\Post(
        path: '/user/update',
        summary: 'Update user data',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['name', 'about', 'avatar'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string', description: 'User name', example: 'John Doe'),
                        // new OA\Property(property: 'password', type: 'string', description: 'User password (optional)', example: 'mySecurePass123'),
                        new OA\Property(property: 'about', type: 'string', description: 'About user', example: 'Software developer from Belgrade'),
                        new OA\Property(property: 'avatar', type: 'string', format: 'binary', description: 'User avatar file')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'User updated'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error')
        ]
    )]
    public function update(UserUpdateServices $request): JsonResponse
    {
        $data = $request->validated();

        $users = $this->userServices->update($data);
        return response()->json($users);
    }    
    
    #[OA\Post(
        path: '/user/change-password',
        summary: 'Change user password',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['name', 'about', 'avatar'],
                    properties: [
                        new OA\Property(property: 'password', type: 'string', description: 'User password', example: 'Password123#'),
                        new OA\Property(property: 'password2', type: 'string', description: 'User password repeated', example: 'Password123#'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'User updated'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error')
        ]
    )]
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $users = $this->userServices->changePassword($data);
        return response()->json($users);
    }
}
