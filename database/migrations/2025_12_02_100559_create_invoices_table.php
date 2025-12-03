<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->unsignedBigInteger('lead_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index(); // if you have customers table

            // Invoice fields
            $table->string('invoice_no')->unique();
            $table->enum('status', ['draft','sent','paid','partial','overdue','cancelled'])->default('draft')->index();
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);

            $table->string('currency', 8)->default('INR');
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();

            $table->text('notes')->nullable();
            $table->json('meta')->nullable();

            // PDF path if stored
            $table->string('pdf_path')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('sent_by')->nullable()->index();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            // If you prefer foreign keys add them — commented to avoid FK issues in older setups
            // $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            // $table->foreign('lead_id')->references('id')->on('leads')->nullOnDelete();
            // $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            // $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            // $table->foreign('sent_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
