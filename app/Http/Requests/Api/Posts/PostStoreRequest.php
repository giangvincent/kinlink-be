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
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp', 'max:8192'],
        ];
    }
}
