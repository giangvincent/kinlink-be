<?php

namespace App\Models;

use App\Enums\BillingPlan;
use App\Models\Export;
use App\Models\Pivots\FamilyUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'settings',
        'locale',
        'billing_plan',
        'owner_user_id',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'billing_plan' => BillingPlan::class,
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(FamilyUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function relationships(): HasMany
    {
        return $this->hasMany(Relationship::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function exports(): HasMany
    {
        return $this->hasMany(Export::class);
    }
}
