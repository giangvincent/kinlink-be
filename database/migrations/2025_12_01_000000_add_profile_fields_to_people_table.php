<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            if (! Schema::hasColumn('people', 'is_deceased')) {
                $table->boolean('is_deceased')->default(false)->after('death_date');
            }

            if (! Schema::hasColumn('people', 'closest_relationship')) {
                $table->string('closest_relationship')->nullable()->after('meta');
            }

            if (! Schema::hasColumn('people', 'closest_relative_id')) {
                $table->foreignId('closest_relative_id')
                    ->nullable()
                    ->after('closest_relationship')
                    ->constrained('people')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            if (Schema::hasColumn('people', 'closest_relative_id')) {
                $table->dropForeign(['closest_relative_id']);
            }

            $existing = Schema::getColumnListing('people');
            $columns = array_intersect(
                ['is_deceased', 'closest_relationship', 'closest_relative_id'],
                $existing
            );

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
