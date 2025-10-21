<?php

namespace App\Http\Controllers\Api\Events;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Events\EventStoreRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\Person;
use App\Support\FamilyContext;

class EventStoreController extends ApiController
{
    public function __invoke(EventStoreRequest $request, FamilyContext $familyContext)
    {
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            return $this->fail(['message' => ['Family context not set.']], 400);
        }

        $data = $request->validated();

        if (! empty($data['person_id'])) {
            $person = Person::forFamily($familyId)->findOrFail($data['person_id']);
            $this->authorize('view', $person);
        }

        $event = Event::create(array_merge($data, [
            'family_id' => $familyId,
        ]));

        return $this->created(new EventResource($event));
    }
}
