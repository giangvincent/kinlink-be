<?php

namespace App\Http\Requests\Api\Media;

use App\Http\Requests\Api\ApiRequest;

class MediaAttachRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'model_type' => ['required', 'string'],
            'model_id' => ['required', 'integer'],
            'collection' => ['required', 'string', 'max:255'],
            'file_url' => ['required', 'url'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
