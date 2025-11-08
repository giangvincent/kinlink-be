<?php

namespace App\Http\Controllers\Api\People;

use App\Events\PersonCreated;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\People\Concerns\HandlesPersonMedia;
use App\Http\Requests\Api\People\PersonStoreRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Support\FamilyContext;

class PersonStoreController extends ApiController
{
    use HandlesPersonMedia;

    public function __invoke(PersonStoreRequest $request, FamilyContext $familyContext)
    {
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            return $this->fail(['message' => ['Family context not set.']], 400);
        }

        $validated = $request->validated();
        $data = collect($validated)
            ->except([
                'avatar_url',
                'photo_urls',
                'avatar',
                'photos',
                'notify_relatives',
                'clear_death_date',
                'clear_avatar',
                'clear_photos',
            ])
            ->toArray();
        $data['family_id'] = $familyId;

        $notifyRelatives = $request->boolean('notify_relatives', true);

        if ($request->boolean('clear_death_date')) {
            $data['death_date'] = null;
            $data['is_deceased'] = false;
        }

        $person = Person::create($data);

        $this->syncPersonMedia($person, [
            'avatar_file' => $request->file('avatar'),
            'avatar_url' => $validated['avatar_url'] ?? null,
            'photo_urls' => $validated['photo_urls'] ?? null,
            'photos_files' => $request->file('photos'),
            'clear_avatar' => $request->boolean('clear_avatar'),
            'clear_photos' => $request->boolean('clear_photos'),
        ]);

        $person->load(['closestRelative', 'media']);

        if ($notifyRelatives) {
            event(new PersonCreated($person));
        }

        return $this->created(new PersonResource($person));
    }
}
