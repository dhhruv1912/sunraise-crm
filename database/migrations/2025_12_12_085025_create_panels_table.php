<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('passport_size_photo')->nullable();
            $table->unsignedBigInteger('cancel_cheque')->nullable();

            $table->string('branch_name');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            if (Schema::hasColumn('customers', 'passport_size_photo')) {
                $table->dropColumn('passport_size_photo');
            }
            if (Schema::hasColumn('customers', 'cancel_cheque')) {
                $table->dropColumn('cancel_cheque');
            }
            if (Schema::hasColumn('customers', 'branch_name')) {
                $table->dropColumn('branch_name');
            }
        });
    }
};
