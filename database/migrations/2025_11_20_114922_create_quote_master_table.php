<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_master', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku', 255)->nullable();
            $table->string('module', 255)->nullable();
            $table->decimal('KW', 5,2)->nullable();
            $table->integer('module_count')->nullable();
            $table->decimal('value', 15,2)->nullable();
            $table->decimal('taxes', 15,2)->nullable();
            $table->decimal('metering_cost', 15,2)->nullable();
            $table->decimal('mcb_ppa', 15,2)->nullable();
            $table->decimal('payable', 15,2)->nullable();
            $table->decimal('subsidy', 15,2)->nullable();
            $table->decimal('projected', 15,2)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_master');
    }
};
