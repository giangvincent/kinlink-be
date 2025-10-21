<?php

namespace App\Http\Controllers\Api\People;

use App\Http\Controllers\Api\ApiController;
use App\Models\Person;
use App\Services\KinshipService;
use Illuminate\Http\Request;

class PersonKinshipController extends ApiController
{
    public function __invoke(Request $request, Person $person, KinshipService $kinshipService)
    {
        $this->authorize('view', $person);

        $validated = $request->validate([
            'to' => ['required', 'integer', 'exists:people,id'],
        ]);

        $target = Person::forFamily($person->family_id)->findOrFail($validated['to']);

        $this->authorize('view', $target);

        $path = $kinshipService->relationshipPath($person, $target);

        return $this->ok([
            'path' => $path,
            'label' => $kinshipService->relationshipLabel($path),
        ]);
    }
}
