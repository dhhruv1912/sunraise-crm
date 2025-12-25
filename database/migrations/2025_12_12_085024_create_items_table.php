<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('model')->nullable();
            $table->integer('watt')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('category_id')->references('id')->on('item_categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
