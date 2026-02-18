<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Core
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('mobile')->nullable()->index();
            $table->string('alternate_mobile')->nullable();
            $table->text('address')->nullable();
            $table->longText('note')->nullable();

            // Light-bill details
            $table->string('lightbill_customer_id')->nullable();
            $table->string('lightbill_service_number')->nullable();
            $table->string('sanction_load')->nullable();

            // Bank / cheque
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('micr_code')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('ac_holder_name')->nullable();

            // meta json
            $table->json('meta')->nullable();

            $table->timestamps();

            // Optional indexes (helpful)
            $table->index(['name']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
