<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Email verification timestamp
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }

            // Remember me token
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('password');
            }

            // For multi-company access (sunraise / arham)
            if (!Schema::hasColumn('users', 'company_access')) {
                $table->json('company_access')->nullable()->after('activity');
            }

            // (Optional) For password reset
            if (!Schema::hasColumn('users', 'reset_token')) {
                $table->string('reset_token', 255)->nullable()->after('password_d');
            }

            // (Optional) If you want login with email OR mobile
            // Make them nullable safely (only if not already)
            if (Schema::hasColumn('users', 'email')) {
                $table->string('email', 255)->nullable()->change();
            }
            if (Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile', 20)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('users', 'company_access')) {
                $table->dropColumn('company_access');
            }
            if (Schema::hasColumn('users', 'reset_token')) {
                $table->dropColumn('reset_token');
            }
        });
    }
};
