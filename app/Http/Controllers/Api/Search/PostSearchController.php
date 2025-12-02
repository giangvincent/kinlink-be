<?php

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Search\PostSearchRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Support\FamilyContext;
use Throwable;

class PostSearchController extends ApiController
{
    public function __invoke(PostSearchRequest $request, FamilyContext $familyContext)
    {
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            return $this->fail(['message' => ['Family context not set.']], 400);
        }

        $query = $request->validated()['query'];
        $results = collect();

        if (config('scout.driver') && config('scout.driver') !== 'null') {
            try {
                $results = Post::search($query)
                    ->where('family_id', $familyId)
                    ->take(15)
                    ->get();
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        if ($results->isEmpty()) {
            $results = Post::forFamily($familyId)
                ->where('body', 'like', "%{$query}%")
                ->orderByDesc('created_at')
                ->limit(15)
                ->get();
        }

        return $this->ok(PostResource::collection($results));
    }
}
