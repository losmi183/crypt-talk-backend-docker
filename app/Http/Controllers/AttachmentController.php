<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JWTServices;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AttachmentServices;
use App\Http\Requests\AttachmentSendRequest;
use Symfony\Component\HttpFoundation\Response;

class AttachmentController extends Controller
{
    private JWTServices $jwtServices;
    private AttachmentServices $attachmentServices;
        
    public function __construct(AttachmentServices $attachmentServices, JWTServices $jwtServices) {
        $this->jwtServices = $jwtServices;
        $this->attachmentServices = $attachmentServices;
    }

    #[OA\Post(
        path: '/conversation/send-attachment',
        summary: 'Send attachment (image or video)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['conversation_id', 'file'],
                    properties: [
                        new OA\Property(
                            property: 'conversation_id',
                            type: 'integer',
                            example: 1
                        ),
                        new OA\Property(
                            property: 'file',
                            type: 'string',
                            format: 'binary',
                            description: 'Image or video file (jpg, png, gif, mp4, mov, avi)'
                        ),
                    ]
                )
            )
        ),
        tags: ['Message'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Attachment sent successfully'
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation error'
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Server error'
            ),
        ]
    )]

    public function sendAttachment(AttachmentSendRequest $request): JsonResponse
    {
        $data = $request->validated();

        $attachment = $this->attachmentServices->sendAttachment($data);        

        return response()->json(['status' => 'success', 'attachment' => $attachment]);
    }

    public function show(Request $request, string $type, string $file)
    {
        $token = $request->query('token');
        $conversation_id = $request->query('conversationId');

        $status = $this->jwtServices->decodeJWT($token);

        if ($status == 403) {
            abort( 401, 'Refresh token has expired');
        }
        if ($status != 200) {
            abort(400, 'Token not valid');
        }

        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];

        // If user is in conversation 
        $conversation_user = DB::table('conversation_user')->where('user_id', $user_id)->where('conversation_id', $conversation_id)->first();
        if(!$conversation_user) {
            abort(404, 'User not in conversation');
        }


        // Ograniči pristup samo folderu images
        $filePath = storage_path('app/' . $type . '/' . $file);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        // Vrati fajl direktno u browser
        return response()->file($filePath);
    }
}
