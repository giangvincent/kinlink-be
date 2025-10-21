<?php

namespace App\Events;

use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PersonUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Person $person)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('family.'.$this->person->family_id),
            new PrivateChannel('person.'.$this->person->getKey()),
        ];
    }

    public function broadcastWith(): array
    {
        $request = new \Illuminate\Http\Request();

        return [
            'person' => PersonResource::make($this->person)->toArray($request),
        ];
    }
}
