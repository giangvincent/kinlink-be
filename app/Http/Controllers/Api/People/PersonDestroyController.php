<?php

namespace App\Http\Controllers\Api\People;

use App\Http\Controllers\Api\ApiController;
use App\Models\Person;

class PersonDestroyController extends ApiController
{
    public function __invoke(Person $person)
    {
        $this->authorize('delete', $person);

        $person->delete();

        return $this->noContent();
    }
}
