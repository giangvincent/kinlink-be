<?php

namespace App\Models;

use App\Enums\InvitationRole;
use App\Models\Concerns\BelongsToFamily;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use BelongsToFamily;
    use HasFactory;

    protected $fillable = [
        'family_id',
        'email',
        'role',
        'token',
        'expires_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
            'role' => InvitationRole::class,
        ];
    }
}
