<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->unsignedBigInteger('quote_master_id')->nullable()->index(); // optional link to quote_master
            $table->string('description')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0); // unit_price * qty + tax - line_discount if any
            $table->json('meta')->nullable();
            $table->timestamps();

            // $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
            // $table->foreign('quote_master_id')->references('id')->on('quote_master')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
}
