<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();

            // BASIC REQUEST INFO
            $table->string('type')->nullable();          // request type (call,quote request)
            $table->string('name')->nullable();          // customer name
            $table->string('number')->nullable();        // phone number
            $table->string('email')->nullable();         // email

            // TECHNICAL FIELDS
            $table->string('module')->nullable();        // module brand
            $table->decimal('kw', 10, 2)->nullable();    // system size
            $table->integer('mc')->nullable();           // module count
            $table->integer('budget')->nullable();       // budget

            // STATUS
            $table->enum('status', [
                'new_request',
                'viewed',
                'pending',
                'responded',
                'called',
                'called_converted_to_lead',
                'called_closed'
            ])->default('new_request')->index();

            // OPTIONAL (recommended)
            $table->unsignedBigInteger('assigned_to')->nullable()->index(); // marketing agent
            $table->unsignedBigInteger('created_by')->nullable()->index();  // internal user (optional)

            $table->text('notes')->nullable();          // internal notes
            $table->string('source')->nullable();       // website, adwords, phone, whatsapp
            $table->string('ip')->nullable();           // user IP for analytics
            $table->string('location')->nullable();     // user location

            $table->timestamps();

            // FOREIGN KEYS
            // $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            // $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_requests');
    }
}
