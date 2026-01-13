<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class AttachmentSendRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'conversation_id' => 'required|exists:conversations,id',
            'file' => 'required|file|max:51200|mimes:jpg,jpeg,png,gif,webp,avif,mp4,mov,avi,mp3,wav,m4a,ogg,webm',
        ];
    }
 
    /**
     * @param Validator $validator
     *
     * @return void
     */
    public function failedValidation(Validator $validator): void
    {
        abort(418, $validator->errors());
    }

    /**
     * Setovanje neobaveznih polja na null ako nisu poslata
     * @return void
     */
    // public function prepareForValidation(): void
    // {
    //     if (!array_key_exists('name', $this->all())) {
    //         $this->merge(['name' => 'ssssss']);
    //     }      
    // }
}
