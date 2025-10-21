<?php

namespace App\Services;

use App\Enums\RelationshipType;
use App\Models\Person;
use App\Models\Relationship;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PersonMergeService
{
    public function merge(Person $source, Person $target): void
    {
        if ($source->family_id !== $target->family_id) {
            throw new RuntimeException('People must belong to the same family to merge.');
        }

        DB::transaction(function () use ($source, $target) {
            Relationship::query()
                ->where('family_id', $source->family_id)
                ->where(function ($query) use ($source) {
                    $query->where('person_id_a', $source->getKey())
                        ->orWhere('person_id_b', $source->getKey());
                })
                ->each(function (Relationship $relationship) use ($source, $target) {
                    $newA = $relationship->person_id_a === $source->getKey()
                        ? $target->getKey()
                        : $relationship->person_id_a;

                    $newB = $relationship->person_id_b === $source->getKey()
                        ? $target->getKey()
                        : $relationship->person_id_b;

                    $exists = Relationship::query()
                        ->where('family_id', $relationship->family_id)
                        ->where('person_id_a', $newA)
                        ->where('person_id_b', $newB)
                        ->where('type', $relationship->type)
                        ->whereKeyNot($relationship->getKey())
                        ->exists();

                    if ($exists) {
                        $relationship->delete();
                    } else {
                        $relationship->update([
                            'person_id_a' => $newA,
                            'person_id_b' => $newB,
                        ]);
                    }
                });

            $targetMeta = is_array($target->meta) ? $target->meta : [];
            $sourceMeta = is_array($source->meta) ? $source->meta : [];
            $target->meta = array_merge($targetMeta, $sourceMeta);
            $target->save();

            $source->delete();
        });
    }

    public function reparentChild(int $childId, Person $oldParent, int $newParentId): void
    {
        $newParent = Person::findOrFail($newParentId);

        if ($newParent->family_id !== $oldParent->family_id) {
            throw new RuntimeException('Parents must belong to the same family.');
        }

        DB::transaction(function () use ($childId, $oldParent, $newParent) {
            $relationship = Relationship::query()
                ->where('family_id', $oldParent->family_id)
                ->where('type', RelationshipType::PARENT->value)
                ->where('person_id_a', $oldParent->getKey())
                ->where('person_id_b', $childId)
                ->firstOrFail();

            $exists = Relationship::query()
                ->where('family_id', $oldParent->family_id)
                ->where('type', $relationship->type)
                ->where('person_id_a', $newParent->getKey())
                ->where('person_id_b', $childId)
                ->exists();

            if ($exists) {
                $relationship->delete();
            } else {
                $relationship->update(['person_id_a' => $newParent->getKey()]);
            }
        });
    }
}
