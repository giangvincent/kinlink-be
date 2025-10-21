<?php

namespace App\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    protected function ok(mixed $data = null, array $meta = [], int $status = Response::HTTP_OK)
    {
        return ApiResponse::success(
            $this->transformResource($data),
            array_merge($meta, $this->extractResourceMeta($data)),
            $status
        );
    }

    protected function created(mixed $data = null, array $meta = [])
    {
        return ApiResponse::success(
            $this->transformResource($data),
            array_merge($meta, $this->extractResourceMeta($data)),
            Response::HTTP_CREATED
        );
    }

    protected function noContent(array $meta = [])
    {
        return ApiResponse::success(null, $meta, Response::HTTP_NO_CONTENT);
    }

    protected function fail(array $errors, int $status = Response::HTTP_BAD_REQUEST, array $meta = [], mixed $data = null)
    {
        return ApiResponse::error($errors, $status, $meta, $data);
    }

    protected function transformResource(mixed $data): mixed
    {
        if ($data === null) {
            return null;
        }

        if (is_object($data) && method_exists($data, 'toArray')) {
            return $data->toArray(request());
        }

        return $data;
    }

    protected function extractResourceMeta(mixed $data): array
    {
        if (is_object($data) && method_exists($data, 'additionalMeta')) {
            return $data->additionalMeta();
        }

        return [];
    }
}
