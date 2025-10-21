<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiResponse
{
    public static function success(mixed $data = null, array $meta = [], int $status = 200): JsonResponse
    {
        return static::make($data, $meta, [], $status);
    }

    public static function error(array $errors, int $status = 400, array $meta = [], mixed $data = null): JsonResponse
    {
        return static::make($data, $meta, $errors, $status);
    }

    protected static function make(mixed $data, array $meta, array $errors, int $status): JsonResponse
    {
        return response()->json([
            'data' => static::normalizeData($data),
            'meta' => (object) $meta,
            'errors' => $errors,
        ], $status);
    }

    protected static function normalizeData(mixed $data): mixed
    {
        if ($data instanceof ResourceCollection) {
            return $data->toArray(request());
        }

        if ($data instanceof JsonResource) {
            return $data->toArray(request());
        }

        return $data;
    }
}
