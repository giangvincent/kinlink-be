<?php

namespace App\Http\Requests\Api\Invitations;

use App\Http\Requests\Api\ApiRequest;

class InvitationAcceptRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'password' => ['sometimes', 'nullable', 'string', 'min:12'],
        ];
    }
}
