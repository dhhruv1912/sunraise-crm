<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('panels', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('batch_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();

            $table->string('serial_number')->unique();
            $table->string('model')->nullable();
            $table->string('batch_no_copy')->nullable(); // quick search

            $table->string('status')->default('in_stock'); // in_stock | sold | returned | damaged | removed

            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->dateTime('sold_at')->nullable();
            $table->dateTime('installed_at')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('batch_id')->references('id')->on('batches')->nullOnDelete();
            // $table->foreign('item_id')->references('id')->on('items')->nullOnDelete();
            // $table->foreign('warehouse_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            // $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panels');
    }
};
