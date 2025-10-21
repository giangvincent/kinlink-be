<?php

namespace App\Providers;

use App\Enums\FamilyRole;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Export;
use App\Models\Family;
use App\Models\Invitation;
use App\Models\Person;
use App\Models\Post;
use App\Models\Relationship;
use App\Models\Subscription;
use App\Models\User;
use App\Policies\AuditLogPolicy;
use App\Policies\EventPolicy;
use App\Policies\ExportPolicy;
use App\Policies\FamilyPolicy;
use App\Policies\InvitationPolicy;
use App\Policies\PersonPolicy;
use App\Policies\PostPolicy;
use App\Policies\RelationshipPolicy;
use App\Policies\SubscriptionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Family::class => FamilyPolicy::class,
        Person::class => PersonPolicy::class,
        Relationship::class => RelationshipPolicy::class,
        Event::class => EventPolicy::class,
        Post::class => PostPolicy::class,
        Invitation::class => InvitationPolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        AuditLog::class => AuditLogPolicy::class,
        Export::class => ExportPolicy::class,
    ];

    public function boot(): void
    {
        Gate::define('family-role', function ($user, Family $family, FamilyRole|string $role) {
            $requiredRole = $role instanceof FamilyRole ? $role : FamilyRole::from($role);

            return $user->hasFamilyRole($requiredRole, $family);
        });

        Gate::define('impersonate-users', fn ($user) => (bool) $user->is_admin);
    }
}
