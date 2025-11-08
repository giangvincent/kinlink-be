<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api'])
    ->group(function (): void {
        // Authentication
        Route::prefix('auth')->name('api.auth.')->group(function (): void {
            Route::middleware('throttle:auth')->group(function (): void {
                Route::post('register', [\App\Http\Controllers\Api\Auth\RegisterController::class, '__invoke'])
                    ->name('register');
                Route::post('login', [\App\Http\Controllers\Api\Auth\LoginController::class, '__invoke'])
                    ->name('login');
                Route::get('social/{provider}/redirect', [\App\Http\Controllers\Api\Auth\SocialRedirectController::class, '__invoke'])
                    ->name('social.redirect');
                Route::get('social/{provider}/callback', [\App\Http\Controllers\Api\Auth\SocialCallbackController::class, '__invoke'])
                    ->name('social.callback');
            });

            Route::post('logout', [\App\Http\Controllers\Api\Auth\LogoutController::class, '__invoke'])
                ->middleware('auth:sanctum')
                ->name('logout');
            Route::get('me', [\App\Http\Controllers\Api\Auth\MeController::class, '__invoke'])
                ->middleware('auth:sanctum')
                ->name('me');
        });

        Route::middleware('auth:sanctum')->group(function (): void {
            // Families & Memberships
            Route::prefix('families')->name('api.families.')->group(function (): void {
                Route::get('/', [\App\Http\Controllers\Api\Family\FamilyIndexController::class, '__invoke'])
                    ->name('index');
                Route::post('/', [\App\Http\Controllers\Api\Family\FamilyStoreController::class, '__invoke'])
                    ->name('store');
                Route::get('{family}', [\App\Http\Controllers\Api\Family\FamilyShowController::class, '__invoke'])
                    ->name('show');
                Route::post('{family}/switch', [\App\Http\Controllers\Api\Family\FamilySwitchController::class, '__invoke'])
                    ->name('switch');
            });

            Route::get('memberships', [\App\Http\Controllers\Api\Family\MembershipIndexController::class, '__invoke'])
                ->name('api.memberships.index');

            Route::put('profile', [\App\Http\Controllers\Api\Profile\ProfileUpdateController::class, '__invoke'])
                ->name('api.profile.update');

            // People & Relationships
            Route::get('people', [\App\Http\Controllers\Api\People\PersonIndexController::class, '__invoke'])
                ->name('api.people.index');
            Route::post('people', [\App\Http\Controllers\Api\People\PersonStoreController::class, '__invoke'])
                ->name('api.people.store');
            Route::get('people/{person}', [\App\Http\Controllers\Api\People\PersonShowController::class, '__invoke'])
                ->name('api.people.show');
            Route::put('people/{person}', [\App\Http\Controllers\Api\People\PersonUpdateController::class, '__invoke'])
                ->name('api.people.update');
            Route::delete('people/{person}', [\App\Http\Controllers\Api\People\PersonDestroyController::class, '__invoke'])
                ->name('api.people.destroy');
            Route::get('people/{person}/kinship', [\App\Http\Controllers\Api\People\PersonKinshipController::class, '__invoke'])
                ->name('api.people.kinship');

            Route::post('relationships', [\App\Http\Controllers\Api\Relationships\RelationshipStoreController::class, '__invoke'])
                ->name('api.relationships.store');
            Route::delete('relationships/{relationship}', [\App\Http\Controllers\Api\Relationships\RelationshipDestroyController::class, '__invoke'])
                ->name('api.relationships.destroy');

            // Events & Posts
            Route::get('events', [\App\Http\Controllers\Api\Events\EventIndexController::class, '__invoke'])
                ->name('api.events.index');
            Route::post('events', [\App\Http\Controllers\Api\Events\EventStoreController::class, '__invoke'])
                ->name('api.events.store');

            Route::get('posts', [\App\Http\Controllers\Api\Posts\PostIndexController::class, '__invoke'])
                ->name('api.posts.index');
            Route::post('posts', [\App\Http\Controllers\Api\Posts\PostStoreController::class, '__invoke'])
                ->name('api.posts.store');
            Route::delete('posts/{post}', [\App\Http\Controllers\Api\Posts\PostDestroyController::class, '__invoke'])
                ->name('api.posts.destroy');

            // Invitations
            Route::post('invitations', [\App\Http\Controllers\Api\Invitations\InvitationStoreController::class, '__invoke'])
                ->middleware('throttle:invites')
                ->name('api.invitations.store');
        });

        Route::get('invitations/{token}', [\App\Http\Controllers\Api\Invitations\InvitationShowController::class, '__invoke'])
            ->name('api.invitations.show');
        Route::post('invitations/{token}/accept', [\App\Http\Controllers\Api\Invitations\InvitationAcceptController::class, '__invoke'])
            ->name('api.invitations.accept');

        Route::middleware('auth:sanctum')->group(function (): void {
            // Media
            Route::post('uploads/sign', [\App\Http\Controllers\Api\Media\UploadSignatureController::class, '__invoke'])
                ->name('api.media.sign');
            Route::post('media/attach', [\App\Http\Controllers\Api\Media\MediaAttachController::class, '__invoke'])
                ->name('api.media.attach');
            Route::get('media/{modelType}/{model}', [\App\Http\Controllers\Api\Media\MediaIndexController::class, '__invoke'])
                ->name('api.media.index');
            Route::delete('media/{uuid}', [\App\Http\Controllers\Api\Media\MediaDestroyController::class, '__invoke'])
                ->name('api.media.destroy');

            // Search
            Route::get('search/people', [\App\Http\Controllers\Api\Search\PeopleSearchController::class, '__invoke'])
                ->name('api.search.people');
            Route::get('search/posts', [\App\Http\Controllers\Api\Search\PostSearchController::class, '__invoke'])
                ->name('api.search.posts');

            // Billing & subscriptions
            Route::get('billing/plan', [\App\Http\Controllers\Api\Billing\PlanShowController::class, '__invoke'])
                ->name('api.billing.plan');
            Route::post('billing/checkout', [\App\Http\Controllers\Api\Billing\CheckoutController::class, '__invoke'])
                ->name('api.billing.checkout');
            Route::get('billing/usage', [\App\Http\Controllers\Api\Billing\UsageController::class, '__invoke'])
                ->name('api.billing.usage');

            // Realtime auth
            Route::get('realtime/auth', [\App\Http\Controllers\Api\Realtime\RealtimeAuthController::class, '__invoke'])
                ->name('api.realtime.auth');

            // Exports
            Route::post('exports/family-book', [\App\Http\Controllers\Api\Exports\FamilyBookExportController::class, '__invoke'])
                ->name('api.exports.family-book');
            Route::get('exports/{export}', [\App\Http\Controllers\Api\Exports\ExportShowController::class, '__invoke'])
                ->name('api.exports.show');
        });

        Route::post('webhooks/stripe', [\App\Http\Controllers\Api\Billing\StripeWebhookController::class, '__invoke'])
            ->name('api.billing.webhook');
    });
