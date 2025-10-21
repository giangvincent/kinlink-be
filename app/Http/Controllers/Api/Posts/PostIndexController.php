<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Posts\PostIndexRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Support\FamilyContext;

class PostIndexController extends ApiController
{
    public function __invoke(PostIndexRequest $request, FamilyContext $familyContext)
    {
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            return $this->fail(['message' => ['Family context not set.']], 400);
        }

        $filters = $request->validated();

        $posts = Post::forFamily($familyId)
            ->with('author')
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->cursorPaginate(20, ['*'], 'cursor', $filters['cursor'] ?? null);

        return $this->ok([
            'items' => PostResource::collection($posts->items())->toArray($request),
            'pagination' => [
                'next_cursor' => optional($posts->nextCursor())->encode(),
                'previous_cursor' => optional($posts->previousCursor())->encode(),
                'per_page' => $posts->perPage(),
            ],
        ]);
    }
}
