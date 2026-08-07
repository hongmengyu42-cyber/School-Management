<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('department_code', 100)->unique();
                $table->string('department_name');
                $table->timestamps();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['Admin', 'Teacher', 'Student', 'Parent'])->default('Student')->after('email');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['Pending', 'Active', 'Suspended'])->default('Pending')->after('role');
            }

            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('status')->constrained('departments')->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('department_id');
            }

            if (!Schema::hasColumn('users', 'theme_preference')) {
                $table->string('theme_preference', 20)->nullable()->after('profile_picture');
            }

            if (!Schema::hasColumn('users', 'language')) {
                $table->string('language', 20)->default('en')->after('theme_preference');
            }

            // two_factor_secret / two_factor_confirmed_at / two_factor_recovery_codes are added
            // separately by Laravel Fortify's own migration - not duplicated here.
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department_id')) {
                $table->dropConstrainedForeignId('department_id');
            }

            $columns = [];

            if (Schema::hasColumn('users', 'role')) {
                $columns[] = 'role';
            }

            if (Schema::hasColumn('users', 'status')) {
                $columns[] = 'status';
            }

            if (Schema::hasColumn('users', 'profile_picture')) {
                $columns[] = 'profile_picture';
            }

            if (Schema::hasColumn('users', 'theme_preference')) {
                $columns[] = 'theme_preference';
            }

            if (Schema::hasColumn('users', 'language')) {
                $columns[] = 'language';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
