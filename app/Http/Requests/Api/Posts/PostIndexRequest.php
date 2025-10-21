<?php

namespace App\Http\Requests\Api\Posts;

use App\Http\Requests\Api\ApiRequest;

class PostIndexRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'cursor' => ['nullable', 'string'],
        ];
    }
}
