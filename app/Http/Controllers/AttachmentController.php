<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use App\Services\AttachmentServices;
use App\Http\Requests\AttachmentSendRequest;
use Symfony\Component\HttpFoundation\Response;

class AttachmentController extends Controller
{
    private AttachmentServices $attachmentServices;
    
    public function __construct(AttachmentServices $attachmentServices) {
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
}
