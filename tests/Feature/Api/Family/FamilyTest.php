<?php

namespace Tests\Feature\Api\Family;

use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FamilyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_list_their_families(): void
    {
        // Create multiple families with the user as a member
        $families = Family::factory()->count(3)->create();
        foreach ($families as $family) {
            $family->members()->attach($this->user, ['role' => 'owner']);
        }

        // Create a family that the user is not a member of
        Family::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('api.families.index'));

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ]]
            ]);
    }

    public function test_user_can_create_a_family(): void
    {
        Sanctum::actingAs($this->user);

        $familyData = [
            'name' => 'New Family',
        ];

        $response = $this->postJson(route('api.families.store'), $familyData);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertDatabaseHas('families', [
            'name' => 'New Family',
        ]);

        // Verify that the user is attached as an admin
        $familyId = $response->json('data.id');
        $this->assertDatabaseHas('family_user', [
            'family_id' => $familyId,
            'user_id' => $this->user->id,
            'role' => 'owner',
        ]);
    }

    public function test_user_can_view_family_details(): void
    {
        $family = Family::factory()->create();
        $family->members()->attach($this->user, ['role' => 'owner']);

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('api.families.show', $family));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    public function test_unauthorized_user_cannot_view_family_details(): void
    {
        $family = Family::factory()->create();
        // Not attaching the user to this family

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('api.families.show', $family));

        $response->assertForbidden();
    }

    public function test_user_can_switch_active_family(): void
    {
        $family = Family::factory()->create();
        $family->members()->attach($this->user, ['role' => 'owner']);

        Sanctum::actingAs($this->user);

        $response = $this->postJson(route('api.families.switch', $family));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
                'meta' => [
                    'family_id',
                    'suggested_headers' => [
                        'X-Family-ID'
                    ]
                ]
            ])
            ->assertJson([
                'meta' => [
                    'family_id' => $family->id,
                    'suggested_headers' => [
                        'X-Family-ID' => $family->id
                    ]
                ]
            ]);
    }

    public function test_user_cannot_switch_to_unauthorized_family(): void
    {
        $family = Family::factory()->create();
        // Not attaching the user to this family

        Sanctum::actingAs($this->user);

        $response = $this->postJson(route('api.families.switch', $family));

        $response->assertForbidden();
    }
}