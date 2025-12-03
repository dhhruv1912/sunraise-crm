<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('marketing_data_id')->nullable();
            $table->text('message')->nullable();
            $table->integer('assignee')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_log');
    }
};
