<?php

namespace App\Http\Controllers\Api\Relationships;

use App\Http\Controllers\Api\ApiController;
use App\Models\Relationship;

class RelationshipDestroyController extends ApiController
{
    public function __invoke(Relationship $relationship)
    {
        $this->authorize('delete', $relationship);

        $relationship->delete();

        return $this->noContent();
    }
}
