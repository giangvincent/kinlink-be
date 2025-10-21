<?php

namespace Tests\Feature\Policies;

use App\Enums\FamilyRole;
use App\Models\Family;
use App\Models\Invitation;
use App\Models\Person;
use App\Models\Relationship;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    public function test_family_role_gate_allows_member_with_required_role(): void
    {
        $user = User::factory()->create();
        $family = Family::factory()->create(['owner_user_id' => $user->getKey()]);
        $member = User::factory()->create();

        $family->members()->attach($member->getKey(), ['role' => FamilyRole::ELDER->value]);

        $this->assertTrue(
            Gate::forUser($member)->allows('family-role', [$family, FamilyRole::MEMBER])
        );
        $this->assertTrue(
            Gate::forUser($member)->allows('family-role', [$family, FamilyRole::ELDER])
        );
        $this->assertFalse(
            Gate::forUser($member)->allows('family-role', [$family, FamilyRole::OWNER])
        );
    }

    public function test_person_policy_prevents_cross_family_access(): void
    {
        $familyA = Family::factory()->create();
        $familyB = Family::factory()->create();

        $memberA = User::factory()->create();
        $familyA->members()->attach($memberA->getKey(), ['role' => FamilyRole::MEMBER->value]);

        $personB = Person::factory()->create(['family_id' => $familyB->getKey()]);

        $this->actingAs($memberA);
        $this->assertFalse($memberA->can('view', $personB));
    }

    public function test_relationship_policy_requires_elder_role_for_updates(): void
    {
        $family = Family::factory()->create();
        $member = User::factory()->create();
        $family->members()->attach($member->getKey(), ['role' => FamilyRole::ELDER->value]);

        $personOne = Person::factory()->create(['family_id' => $family->getKey()]);
        $personTwo = Person::factory()->create(['family_id' => $family->getKey()]);

        $relationship = Relationship::create([
            'family_id' => $family->getKey(),
            'person_id_a' => $personOne->getKey(),
            'person_id_b' => $personTwo->getKey(),
            'type' => 'OTHER',
            'certainty' => 100,
        ]);

        $this->actingAs($member);
        $this->assertTrue($member->can('update', $relationship));

        $family->members()->updateExistingPivot($member->getKey(), ['role' => FamilyRole::MEMBER->value]);
        $member->refresh();

        $this->assertFalse($member->can('update', $relationship));
    }

    public function test_invitation_policy_allows_owner_to_manage(): void
    {
        $owner = User::factory()->create();
        $family = Family::factory()->create(['owner_user_id' => $owner->getKey()]);
        $family->members()->syncWithoutDetaching([$owner->getKey() => ['role' => FamilyRole::OWNER->value]]);

        $invitation = Invitation::create([
            'family_id' => $family->getKey(),
            'email' => 'new@example.com',
            'role' => 'member',
            'token' => 'demo-token',
            'expires_at' => now()->addDay(),
        ]);

        $this->actingAs($owner);
        $this->assertTrue($owner->can('update', $invitation));
    }
}
