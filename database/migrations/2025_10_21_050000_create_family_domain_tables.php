<?php

use App\Enums\BillingPlan;
use App\Enums\EventType;
use App\Enums\FamilyRole;
use App\Enums\InvitationRole;
use App\Enums\PersonGender;
use App\Enums\PersonVisibility;
use App\Enums\PostVisibility;
use App\Enums\RelationshipType;
use App\Enums\SubscriptionProvider;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('settings')->nullable();
            $table->string('locale', 12)->default('en');
            $table->enum('billing_plan', BillingPlan::values())->default(BillingPlan::FREE->value);
            $table->timestamps();
        });

        Schema::create('family_user', function (Blueprint $table) {
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', FamilyRole::values())->default(FamilyRole::MEMBER->value);
            $table->timestamps();

            $table->primary(['family_id', 'user_id']);
            $table->index(['family_id', 'role']);
        });

        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->string('given_name');
            $table->string('middle_name')->nullable();
            $table->string('surname');
            $table->string('display_name');
            $table->enum('gender', PersonGender::values())->default(PersonGender::UNKNOWN->value);
            $table->date('birth_date')->nullable();
            $table->date('death_date')->nullable();
            $table->enum('visibility', PersonVisibility::values())->default(PersonVisibility::FAMILY->value);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['family_id', 'surname']);
            $table->index(['family_id', 'display_name']);
        });

        Schema::create('relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id_a')->constrained('people')->cascadeOnDelete();
            $table->foreignId('person_id_b')->constrained('people')->cascadeOnDelete();
            $table->enum('type', RelationshipType::values());
            $table->unsignedTinyInteger('certainty')->default(100);
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['family_id', 'person_id_a', 'type']);
            $table->index(['family_id', 'person_id_b', 'type']);
            $table->unique(['family_id', 'person_id_a', 'person_id_b', 'type'], 'relationships_unique_pair');
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->enum('type', EventType::values());
            $table->date('date_exact')->nullable();
            $table->json('date_range')->nullable();
            $table->boolean('lunar')->default(false);
            $table->string('place')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['family_id', 'date_exact']);
            $table->index(['family_id', 'type']);
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->enum('visibility', PostVisibility::values())->default(PostVisibility::FAMILY->value);
            $table->boolean('pinned')->default(false);
            $table->timestamps();

            $table->index(['family_id', 'pinned']);
        });

        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->enum('role', InvitationRole::values())->default(InvitationRole::MEMBER->value);
            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['family_id', 'email']);
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->enum('provider', SubscriptionProvider::values());
            $table->enum('status', SubscriptionStatus::values())->default(SubscriptionStatus::ACTIVE->value);
            $table->timestamp('current_period_end')->nullable();
            $table->unsignedInteger('seats')->default(0);
            $table->unsignedInteger('storage_quota_mb')->default(0);
            $table->timestamps();

            $table->unique(['family_id', 'provider']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('target_type');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['family_id', 'action']);
            $table->index(['family_id', 'target_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('events');
        Schema::dropIfExists('relationships');
        Schema::dropIfExists('people');
        Schema::dropIfExists('family_user');
        Schema::dropIfExists('families');
    }
};
