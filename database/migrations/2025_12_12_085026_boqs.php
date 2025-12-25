<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boqs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('boq_no')->unique();
            $table->date('boq_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
        Schema::create('boq_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('boq_id');
            $table->string('item');
            $table->string('specification')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->decimal('rate', 10, 2);
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('boqs');
        Schema::dropIfExists('boq_items');
    }
};
