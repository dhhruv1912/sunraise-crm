<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('method')->nullable(); // cash, bank_transfer, cheque, online
            $table->string('reference')->nullable(); // txn id / cheque no
            $table->date('paid_at')->nullable();
            $table->unsignedBigInteger('received_by')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            // $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
            // $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_payments');
    }
}
