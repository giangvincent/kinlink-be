<?php

namespace App\Models\Pivots;

use App\Enums\FamilyRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FamilyUser extends Pivot
{
    protected $table = 'family_user';

    protected $casts = [
        'role' => FamilyRole::class,
    ];

    protected $fillable = [
        'role',
    ];

    public $timestamps = true;

    /**
     * Ensure the role is stored in lowercase to match enum values.
     */
    protected function role(): Attribute
    {
        return Attribute::set(static fn (FamilyRole|string $value) => $value instanceof FamilyRole ? $value->value : $value);
    }
}
