<?php

namespace Database\Factories;

use App\Enums\RelationshipType;
use App\Models\Family;
use App\Models\Person;
use App\Models\Relationship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Relationship>
 */
class RelationshipFactory extends Factory
{
    protected $model = Relationship::class;

    public function definition(): array
    {
        $family = Family::factory();

        return [
            'family_id' => $family,
            'person_id_a' => Person::factory()->for($family),
            'person_id_b' => Person::factory()->for($family),
            'type' => $this->faker->randomElement(RelationshipType::cases()),
            'certainty' => $this->faker->numberBetween(50, 100),
            'source' => $this->faker->optional()->sentence(3),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
