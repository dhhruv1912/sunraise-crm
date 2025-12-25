<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->unique(); // auto-generated if missing
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();

            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();

            $table->integer('quantity_expected')->nullable();
            $table->integer('quantity_received')->default(0);

            $table->json('meta')->nullable();  // store size, weight, raw OCR text
            $table->string('status')->default('pending'); // pending / received / verified

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            // $table->foreign('warehouse_id')->references('id')->on('warehouse_locations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
