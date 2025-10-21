<?php

namespace Database\Factories;

use App\Enums\BillingPlan;
use App\Models\Family;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Family>
 */
class FamilyFactory extends Factory
{
    protected $model = Family::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->lastName().' Family';
        return [
            'name' => $name,
            'slug' => Str::slug($name.'-'.$this->faker->unique()->randomNumber()),
            'settings' => [],
            'locale' => 'en',
            'billing_plan' => BillingPlan::FREE,
            'owner_user_id' => User::factory(),
        ];
    }
}
