<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('label', 255)->nullable();
            $table->text('value')->nullable();
            $table->integer('type');
            $table->text('option')->nullable();
            $table->text('attr')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->text('default')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting');
    }
};
