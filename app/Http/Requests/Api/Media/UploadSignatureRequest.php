<?php

namespace App\Http\Requests\Api\Media;

use App\Http\Requests\Api\ApiRequest;

class UploadSignatureRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'filename' => ['required', 'string', 'max:255'],
            'contentType' => ['required', 'string', 'max:255'],
        ];
    }
}
