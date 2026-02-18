<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteMasterTable extends Migration
{
    public function up()
    {
        Schema::create('quote_master', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable()->index();
            $table->string('module')->nullable();
            $table->decimal('kw', 10, 3)->nullable();
            $table->integer('module_count')->nullable();
            $table->decimal('value', 15, 2)->nullable();
            $table->decimal('taxes', 12, 2)->nullable();
            $table->decimal('metering_cost', 12, 2)->nullable();
            $table->decimal('mcb_ppa', 12, 2)->nullable();
            $table->decimal('payable', 15, 2)->nullable();
            $table->decimal('subsidy', 15, 2)->nullable();
            $table->decimal('projected', 15, 2)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_master');
    }
}
