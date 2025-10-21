<?php

namespace App\Events;

use App\Http\Resources\RelationshipResource;
use App\Models\Relationship;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RelationshipCreated implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Relationship $relationship)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('family.'.$this->relationship->family_id),
        ];
    }

    public function broadcastWith(): array
    {
        $request = new \Illuminate\Http\Request();

        return [
            'relationship' => RelationshipResource::make($this->relationship)->toArray($request),
        ];
    }
}
