<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staffId');
            $table->string('location', 50)->nullable();
            $table->text('message')->nullable();
            $table->string('lendmark', 255)->nullable();
            $table->string('device', 100)->nullable();
            $table->string('ip', 45)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_logs');
    }
};
