<?php

namespace App\Http\Requests\Api\Search;

use App\Http\Requests\Api\ApiRequest;

class FamilySearchRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'max:255'],
        ];
    }
}
