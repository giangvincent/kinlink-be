<?php

namespace App\Http\Controllers\Api\People;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Http\Request;

class PersonShowController extends ApiController
{
    public function __invoke(Request $request, Person $person)
    {
        $this->authorize('view', $person);

        $person->load(['primaryRelationships', 'relatedRelationships', 'closestRelative', 'media']);

        return $this->ok(new PersonResource($person));
    }
}
