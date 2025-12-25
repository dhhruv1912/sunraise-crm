<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('panel_movements', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('panel_id');

            $table->string('action'); 
            // received | moved | sold | returned | damaged | removed | assigned

            $table->unsignedBigInteger('from_warehouse_id')->nullable();
            $table->unsignedBigInteger('to_warehouse_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->unsignedBigInteger('performed_by')->nullable();
            $table->text('note')->nullable();

            $table->timestamp('happened_at')->useCurrent();

            $table->timestamps();

            // $table->foreign('panel_id')->references('id')->on('panels')->cascadeOnDelete();
            // $table->foreign('from_warehouse_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            // $table->foreign('to_warehouse_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            // $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panel_movements');
    }
};
