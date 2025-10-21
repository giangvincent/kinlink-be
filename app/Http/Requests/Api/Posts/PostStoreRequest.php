<?php

namespace App\Http\Requests\Api\Posts;

use App\Enums\PostVisibility;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class PostStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string'],
            'visibility' => ['nullable', Rule::in(PostVisibility::values())],
            'pinned' => ['nullable', 'boolean'],
        ];
    }
}
