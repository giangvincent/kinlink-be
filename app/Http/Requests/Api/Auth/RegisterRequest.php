<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiRequest;

class RegisterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Avoid DNS checks in tests/environments without network
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
            'locale' => ['nullable', 'string', 'size:2'],
            'time_zone' => ['nullable', 'string', 'max:64'],
        ];
    }
}
