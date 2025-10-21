<?php

namespace App\Http\Controllers\Api\Family;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\FamilyResource;
use App\Models\Family;
use App\Support\FamilyContext;
use Illuminate\Http\Request;

class FamilySwitchController extends ApiController
{
    public function __invoke(Request $request, Family $family, FamilyContext $familyContext)
    {
        $this->authorize('view', $family);

        $familyContext->set($family);

        return $this->ok(
            new FamilyResource($family),
            meta: [
                'family_id' => $family->getKey(),
                'suggested_headers' => [
                    'X-Family-ID' => $family->getKey(),
                ],
            ]
        );
    }
}
