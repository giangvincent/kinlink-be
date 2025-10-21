<?php

namespace App\Http\Controllers\Api\People;

use App\Events\PersonCreated;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\People\PersonStoreRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Support\FamilyContext;

class PersonStoreController extends ApiController
{
    public function __invoke(PersonStoreRequest $request, FamilyContext $familyContext)
    {
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            return $this->fail(['message' => ['Family context not set.']], 400);
        }

        $data = $request->validated();
        $data['family_id'] = $familyId;

        $person = Person::create($data);

        event(new PersonCreated($person));

        return $this->created(new PersonResource($person));
    }
}
