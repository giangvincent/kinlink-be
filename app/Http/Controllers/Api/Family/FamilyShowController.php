<?php

namespace App\Http\Controllers\Api\Family;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\FamilyResource;
use App\Models\Family;
use Illuminate\Http\Request;

class FamilyShowController extends ApiController
{
    public function __invoke(Request $request, Family $family)
    {
        $this->authorize('view', $family);

        $family->load(['members']);

        return $this->ok(new FamilyResource($family));
    }
}
