<?php

namespace Database\Seeders;

use App\Enums\EventType;
use App\Enums\FamilyRole;
use App\Enums\PostVisibility;
use App\Enums\RelationshipType;
use App\Models\Event;
use App\Models\Family;
use App\Models\Person;
use App\Models\Post;
use App\Models\Relationship;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'demo@kinlink.test'],
            [
                'name' => 'Demo Owner',
                'password' => bcrypt('password-demo'),
                'locale' => 'en',
                'time_zone' => 'UTC',
                'is_admin' => true,
            ]
        );

        $family = Family::firstOrCreate(
            ['slug' => 'demo-kinlink-family'],
            [
                'name' => 'Demo KinLink Family',
                'locale' => 'en',
                'settings' => [],
                'owner_user_id' => $owner->getKey(),
            ]
        );

        $family->members()->syncWithoutDetaching([
            $owner->getKey() => ['role' => FamilyRole::OWNER->value],
        ]);

        // Reset existing demo content
        $family->relationships()->delete();
        $family->events()->delete();
        $family->posts()->delete();
        $family->people()->delete();

        $people = Person::factory()
            ->count(25)
            ->state(fn () => ['family_id' => $family->getKey()])
            ->create();

        $personIds = $people->pluck('id')->shuffle();

        // Create sample relationships
        foreach ($personIds->chunk(2) as $chunk) {
            $pair = $chunk->values();

            if ($pair->count() < 2) {
                continue;
            }

            Relationship::create([
                'family_id' => $family->getKey(),
                'person_id_a' => $pair[0],
                'person_id_b' => $pair[1],
                'type' => RelationshipType::OTHER,
                'certainty' => random_int(70, 100),
            ]);
        }

        // Sample events
        foreach ($people->take(5) as $person) {
            Event::create([
                'family_id' => $family->getKey(),
                'person_id' => $person->getKey(),
                'type' => EventType::BIRTH,
                'date_exact' => $person->birth_date,
                'place' => 'Sample City',
            ]);
        }

        // Sample posts
        Post::create([
            'family_id' => $family->getKey(),
            'author_user_id' => $owner->getKey(),
            'body' => 'Welcome to the KinLink demo family! Share your memories here.',
            'visibility' => PostVisibility::FAMILY,
            'pinned' => true,
        ]);

        // Index for Scout if driver configured
        if (config('scout.driver')) {
            $people->searchable();
            Post::query()->where('family_id', $family->getKey())->get()->searchable();
        }
    }
}
