<?php

namespace App\Events;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreated implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Post $post)
    {
        $this->post->loadMissing('author');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('family.'.$this->post->family_id),
        ];
    }

    public function broadcastWith(): array
    {
        $request = new \Illuminate\Http\Request();

        return [
            'post' => PostResource::make($this->post)->toArray($request),
        ];
    }
}
