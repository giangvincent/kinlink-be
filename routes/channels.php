<?php

use App\Models\Person;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('family.{familyId}', function ($user, int $familyId) {
    return $user->isFamilyMember($familyId);
});

Broadcast::channel('person.{personId}', function ($user, int $personId) {
    $person = Person::find($personId);

    return $person && $user->isFamilyMember($person->family_id);
});
