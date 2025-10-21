<?php

namespace App\Http\Requests\Api\People;

use App\Http\Requests\Api\ApiRequest;

class PersonIndexRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'generation' => ['nullable', 'integer', 'between:-10,10'],
            'surname' => ['nullable', 'string', 'max:255'],
        ];
    }
}
