<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('panel_attachments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('batch_id')->nullable();
            $table->unsignedBigInteger('panel_id')->nullable();

            $table->string('path'); // file path
            $table->string('type'); // invoice | image | generated_invoice

            $table->string('original_filename')->nullable();
            $table->longText('ocr_text')->nullable();
            $table->json('structured_data')->nullable(); // parsed header + serials

            $table->unsignedBigInteger('uploaded_by')->nullable();

            $table->timestamps();

            // $table->foreign('batch_id')->references('id')->on('batches')->cascadeOnDelete();
            // $table->foreign('panel_id')->references('id')->on('panels')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panel_attachments');
    }
};
