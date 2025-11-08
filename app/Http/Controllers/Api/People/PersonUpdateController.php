<?php

namespace App\Http\Controllers\Api\People;

use App\Events\PersonUpdated;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\People\Concerns\HandlesPersonMedia;
use App\Http\Requests\Api\People\PersonUpdateRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;

class PersonUpdateController extends ApiController
{
    use HandlesPersonMedia;

    public function __invoke(PersonUpdateRequest $request, Person $person)
    {
        $this->authorize('update', $person);

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
        $notifyRelatives = $request->boolean('notify_relatives', true);

        if ($request->boolean('clear_death_date')) {
            $data['death_date'] = null;
            $data['is_deceased'] = false;
        }

        $person->fill($data);
        $person->save();

        $this->syncPersonMedia($person, [
            'avatar_file' => $request->file('avatar'),
            'avatar_url' => $validated['avatar_url'] ?? null,
            'photos_files' => $request->file('photos'),
            'photo_urls' => $validated['photo_urls'] ?? null,
            'clear_avatar' => $request->boolean('clear_avatar'),
            'clear_photos' => $request->boolean('clear_photos'),
        ]);

        $person->load(['closestRelative', 'media']);

        if ($notifyRelatives) {
            event(new PersonUpdated($person));
        }

        return $this->ok(new PersonResource($person));
    }
}
