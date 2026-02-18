<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Drop columns safely
            if (Schema::hasColumn('users', 'password_d')) {
                $table->dropColumn('password_d');
            }

            if (Schema::hasColumn('users', 'activity')) {
                $table->dropColumn('activity');
            }

            if (Schema::hasColumn('users', 'varify')) {
                $table->dropColumn('varify');
            }

            if (Schema::hasColumn('users', 'key')) {
                $table->dropColumn('key');
            }

            // Add default value to role
            if (Schema::hasColumn('users', 'role')) {
                $table->integer('role')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Restore dropped columns (if migration is rolled back)
            $table->string('password_d', 255)->nullable();
            $table->tinyInteger('activity')->nullable();
            $table->tinyInteger('varify')->nullable();
            $table->text('key')->nullable();

            // Remove default role
            $table->integer('role')->change();
        });
    }
};
