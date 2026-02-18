<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fname', 255);
            $table->string('lname', 255);
            $table->string('password', 255);
            $table->text('key')->nullable();
            $table->string('password_d', 255)->nullable();
            $table->integer('role');
            $table->tinyInteger('status');
            $table->string('mobile', 20);
            $table->string('email', 255)->unique();
            $table->tinyInteger('activity');
            $table->decimal('salary', 10, 2)->nullable();
            $table->tinyInteger('varify');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
