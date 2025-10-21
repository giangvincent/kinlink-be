<?php

namespace App\Http\Requests\Api\Invitations;

use App\Enums\InvitationRole;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class InvitationStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns'],
            'role' => ['required', Rule::in(InvitationRole::values())],
        ];
    }
}
