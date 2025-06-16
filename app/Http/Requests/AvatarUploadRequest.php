<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvatarUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048', // 2MB max
//                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Please upload an avatar image.',
            'avatar.image' => 'The uploaded file must be an image.',
            'avatar.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'avatar.max' => 'The image may not be greater than 2MB.',
//            'avatar.dimensions' => 'The image dimensions must be between 100x100 and 2000x2000 pixels.'
        ];
    }
}
