<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Api\ApiController;
use App\Models\Post;

class PostDestroyController extends ApiController
{
    public function __invoke(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return $this->noContent();
    }
}
