<?php

namespace App\Http\Controllers\Api\Posts;

use App\Events\PostCreated;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Posts\PostStoreRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Support\FamilyContext;

class PostStoreController extends ApiController
{
    public function __invoke(PostStoreRequest $request, FamilyContext $familyContext)
    {
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            return $this->fail(['message' => ['Family context not set.']], 400);
        }

        $this->authorize('create', Post::class);

        $post = Post::create([
            'family_id' => $familyId,
            'author_user_id' => $request->user()->getKey(),
            'body' => $request->input('body'),
            'visibility' => $request->input('visibility', 'family'),
            'pinned' => $request->boolean('pinned'),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments', []) as $file) {
                if (! $file) {
                    continue;
                }

                $post->addMedia($file)->toMediaCollection('attachments');
            }
        }

        $post->load('author', 'media');

        event(new PostCreated($post));

        return $this->created(new PostResource($post));
    }
}
