<?php

namespace App\Models;

use App\Enums\FamilyRole;
use App\Models\Pivots\FamilyUser;
use App\Support\FamilyContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'locale',
        'time_zone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cached family roles keyed by family id.
     *
     * @var array<int, FamilyRole|null>
     */
    protected array $familyRoleCache = [];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ownedFamilies(): HasMany
    {
        return $this->hasMany(Family::class, 'owner_user_id');
    }

    public function families(): BelongsToMany
    {
        return $this->belongsToMany(Family::class)
            ->using(FamilyUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function currentFamilyRole(): ?FamilyRole
    {
        return $this->roleInFamily();
    }

    public function roleInFamily(Family|int|null $family = null): ?FamilyRole
    {
        $familyId = $this->resolveFamilyId($family);

        if ($familyId === null) {
            return null;
        }

        if (array_key_exists($familyId, $this->familyRoleCache)) {
            return $this->familyRoleCache[$familyId];
        }

        $family = $this->families()
            ->where('families.id', $familyId)
            ->first();

        if (! $family) {
            return $this->familyRoleCache[$familyId] = null;
        }

        $role = $family->pivot->role;

        return $this->familyRoleCache[$familyId] = $role instanceof FamilyRole
            ? $role
            : FamilyRole::from($role);
    }

    public function hasFamilyRole(FamilyRole $role, Family|int|null $family = null): bool
    {
        $currentRole = $this->roleInFamily($family);

        return $currentRole?->atLeast($role) ?? false;
    }

    public function isFamilyMember(Family|int|null $family = null): bool
    {
        return $this->roleInFamily($family) !== null;
    }

    protected function resolveFamilyId(Family|int|null $family = null): ?int
    {
        if ($family instanceof Family) {
            return $family->getKey();
        }

        if (is_numeric($family)) {
            return (int) $family;
        }

        return app(FamilyContext::class)->currentFamilyId();
    }
}
