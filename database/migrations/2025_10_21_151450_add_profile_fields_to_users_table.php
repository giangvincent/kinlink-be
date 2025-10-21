<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'locale')) {
                $table->string('locale', 12)->default('en')->after('password');
            }

            if (! Schema::hasColumn('users', 'time_zone')) {
                $table->string('time_zone', 64)->default('UTC')->after('locale');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'time_zone')) {
                $table->dropColumn('time_zone');
            }

            if (Schema::hasColumn('users', 'locale')) {
                $table->dropColumn('locale');
            }

            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
