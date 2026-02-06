<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConversationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'conversationId' => 'required',
            'lastMessageId' => 'nullable',
        ];
    }

    /**
     * @param Validator $validator
     *
     * @return void
     */

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                ["errors" => $validator->errors()],
                Response::HTTP_UNPROCESSABLE_ENTITY // 422
            )
        );
}
}
