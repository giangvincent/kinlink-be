<?php

namespace App\Http\Requests\Api\Profile;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'locale' => ['sometimes', 'string', 'max:12'],
            'time_zone' => ['sometimes', 'string', 'max:64'],
            'bio' => ['sometimes', 'nullable', 'string'],
            'avatar' => ['sometimes', 'nullable', 'image', 'max:5120'],
            'clear_avatar' => ['sometimes', 'boolean'],
        ];
    }
}
