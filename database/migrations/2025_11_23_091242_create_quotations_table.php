<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quote_request_id')->nullable()->index();
            $table->string('quotation_no')->nullable()->unique();
            $table->string('pdf_path')->nullable();
            $table->decimal('base_price', 15, 2)->nullable();
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('final_price', 15, 2)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable()->index();
            $table->json('meta')->nullable(); // free-form JSON for extra details
            $table->timestamps();

            // $table->foreign('quote_request_id')->references('id')->on('quote_requests')->cascadeOnDelete();
            // $table->foreign('sent_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
