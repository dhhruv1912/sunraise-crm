<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerNotesTable extends Migration
{
    public function up()
    {
        Schema::create('customer_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // who wrote note
            $table->text('note');
            $table->json('meta')->nullable();
            $table->timestamps();

            // foreign keys optional — comment out if you prefer
            // $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            // $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_notes');
    }
}
