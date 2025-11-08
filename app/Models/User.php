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
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use InteractsWithMedia;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'locale',
        'time_zone',
        'bio',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
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

        $family = $this->families()
            ->where('families.id', $familyId)
            ->first();

        if (! $family) {
            return null;
        }

        $role = $family->pivot->role;

        return $role instanceof FamilyRole
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

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(320)
            ->height(320)
            ->fit('cover', 320, 320)
            ->performOnCollections('avatar')
            ->nonQueued();
    }
}
