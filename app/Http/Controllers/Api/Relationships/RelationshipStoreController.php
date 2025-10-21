<?php

namespace App\Http\Controllers\Api\Relationships;

use App\Events\RelationshipCreated;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Relationships\RelationshipStoreRequest;
use App\Http\Resources\RelationshipResource;
use App\Models\Person;
use App\Models\Relationship;
use App\Support\FamilyContext;

class RelationshipStoreController extends ApiController
{
    public function __invoke(RelationshipStoreRequest $request, FamilyContext $familyContext)
    {
        $data = $request->validated();
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            return $this->fail(['message' => ['Family context not set.']], 400);
        }

        $personA = Person::forFamily($familyId)->findOrFail($data['person_id_a']);
        $personB = Person::forFamily($familyId)->findOrFail($data['person_id_b']);

        $this->authorize('update', $personA);
        $this->authorize('update', $personB);

        $relationship = Relationship::updateOrCreate(
            [
                'family_id' => $familyId,
                'person_id_a' => $personA->getKey(),
                'person_id_b' => $personB->getKey(),
                'type' => $data['type'],
            ],
            [
                'certainty' => $data['certainty'] ?? 100,
                'source' => $data['source'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]
        );

        if ($relationship->wasRecentlyCreated) {
            event(new RelationshipCreated($relationship));

            return $this->created(new RelationshipResource($relationship));
        }

        return $this->ok(new RelationshipResource($relationship));
    }
}
