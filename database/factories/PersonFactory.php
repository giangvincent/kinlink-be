<?php

namespace Database\Factories;

use App\Enums\PersonGender;
use App\Enums\PersonVisibility;
use App\Models\Family;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(PersonGender::cases());
        $given = $this->faker->firstName();
        $middle = $this->faker->optional()->firstName();
        $surname = $this->faker->lastName();

        return [
            'family_id' => Family::factory(),
            'given_name' => $given,
            'middle_name' => $middle,
            'surname' => $surname,
            'display_name' => trim(collect([$given, $middle, $surname])->filter()->implode(' ')),
            'gender' => $gender,
            'birth_date' => $this->faker->optional()->dateTimeBetween('-90 years', '-18 years'),
            'visibility' => $this->faker->randomElement(PersonVisibility::cases()),
            'meta' => [
                'generation' => $this->faker->numberBetween(-3, 3),
            ],
        ];
    }
}
