<?php

namespace App\Http\Requests\Api\Family;

use App\Http\Requests\Api\ApiRequest;

class FamilyStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'size:2'],
        ];
    }
}
