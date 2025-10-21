<?php

namespace App\Http\Controllers\Api\People;

use App\Events\PersonUpdated;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\People\PersonUpdateRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;

class PersonUpdateController extends ApiController
{
    public function __invoke(PersonUpdateRequest $request, Person $person)
    {
        $this->authorize('update', $person);

        $data = $request->validated();

        $person->fill($data);
        $person->save();

        event(new PersonUpdated($person));

        return $this->ok(new PersonResource($person));
    }
}
