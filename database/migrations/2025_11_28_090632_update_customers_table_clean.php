<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {

            // REMOVE old document-related columns
            $table->dropColumn([
                'lightbill_customer_id',
                'lightbill_service_number',
                'sanction_load',

                'bank_name',
                'bank_account_number',
                'ifsc_code',
                'micr_code',
                'branch_name',

            ]);
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {

            // Re-add if rollback
            $table->string('aadhar_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('lightbill_customer_id')->nullable();
            $table->string('lightbill_service_number')->nullable();
            $table->string('lightbill_path')->nullable();
            $table->string('sanction_load')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('micr_code')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('cheque_path')->nullable();

            $table->json('document_data')->nullable();
        });
    }
};
