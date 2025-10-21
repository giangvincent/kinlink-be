<?php

namespace App\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    protected function ok(mixed $data = null, array $meta = [], int $status = Response::HTTP_OK)
    {
        [$normalized, $resourceMeta] = $this->normalizeResource($data);

        return ApiResponse::success(
            $normalized,
            array_merge($meta, $resourceMeta),
            $status
        );
    }

    protected function created(mixed $data = null, array $meta = [])
    {
        [$normalized, $resourceMeta] = $this->normalizeResource($data);

        return ApiResponse::success(
            $normalized,
            array_merge($meta, $resourceMeta),
            Response::HTTP_CREATED
        );
    }

    protected function noContent(array $meta = [])
    {
        return ApiResponse::success(null, $meta, Response::HTTP_NO_CONTENT);
    }

    protected function fail(
        array $errors,
        int $status = Response::HTTP_BAD_REQUEST,
        ?string $title = null,
        ?string $type = null,
        ?string $detail = null
    )
    {
        return ApiResponse::error($errors, $status, $title, $type, $detail);
    }

    protected function normalizeResource(mixed $data): array
    {
        if ($data === null) {
            return [null, []];
        }

        if ($data instanceof ResourceCollection) {
            $response = $data->response()->getData(true);

            $normalized = $response['data'] ?? $response;
            $meta = $response['meta'] ?? [];

            if (isset($response['links'])) {
                $meta['links'] = $response['links'];
            }

            return [$normalized, $meta];
        }

        if ($data instanceof JsonResource) {
            return [$data->resolve(), []];
        }

        if (is_object($data) && method_exists($data, 'toArray')) {
            return [$data->toArray(request()), []];
        }

        return [$data, []];
    }
}
