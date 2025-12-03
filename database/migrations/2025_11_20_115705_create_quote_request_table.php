<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_request', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 50)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('number', 15)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('module', 255)->nullable();
            $table->decimal('kw', 5, 2)->nullable();
            $table->integer('status')->nullable();
            $table->integer('quote_master_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_request');
    }
};
