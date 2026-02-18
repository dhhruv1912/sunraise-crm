<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Add new field
            $table->unsignedBigInteger('quote_request_id')->nullable()->after('id');

            // Drop old fields
            $table->dropColumn([
                'customer_name',
                'mobile',
                'address',
                'latitude',
                'longitude',
                'kw',
                'module_brand',
                'inverter_brand',
                'module_count',
                'project_value',
                'aadhar_card_number',
                'aadhar_card',
                'pan_card_number',
                'pan_card',
                'lightbill_number',
                'light_bill',
                'sanction_load',
                'service_number',
                'bank_account_number',
                'micr_code',
                'ifsc_code',
                'bank_name',
                'ac_holder_name',
                'branch_name',
                'cancel_cheque',
                'passport_size_photo',
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('aadhar_card_number')->nullable();
            $table->integer('aadhar_card')->nullable();
            $table->string('pan_card_number')->nullable();
            $table->integer('pan_card')->nullable();
            $table->string('lightbill_number')->nullable();
            $table->integer('light_bill')->nullable();
            $table->string('sanction_load')->nullable();
            $table->string('service_number')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('micr_code')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->integer('cancel_cheque')->nullable();
            $table->integer('passport_size_photo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['quote_request_id']);
            $table->dropColumn('quote_request_id');

            // Re-add dropped columns if rollback needed
            $table->string('customer_name')->nullable();
            $table->string('mobile')->nullable();
            $table->text('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('kw')->nullable();
            $table->string('module_brand')->nullable();
            $table->string('inverter_brand')->nullable();
            $table->integer('module_count')->nullable();
            $table->decimal('project_value', 10, 2)->nullable();
            $table->string('aadhar_card_number')->nullable();
            $table->string('aadhar_card')->nullable();
            $table->string('pan_card_number')->nullable();
            $table->string('pan_card')->nullable();
            $table->string('lightbill_number')->nullable();
            $table->string('light_bill')->nullable();
            $table->string('sanction_load')->nullable();
            $table->string('service_number')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('micr_code')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ac_holder_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('cancel_cheque')->nullable();
            $table->string('passport_size_photo')->nullable();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'aadhar_card_number',
                'aadhar_card',
                'pan_card_number',
                'pan_card',
                'lightbill_number',
                'light_bill',
                'sanction_load',
                'service_number',
                'bank_account_number',
                'micr_code',
                'ifsc_code',
                'bank_name',
                'ac_holder_name',
                'branch_name',
                'cancel_cheque',
                'passport_size_photo',
            ]);
        });
    }
};
