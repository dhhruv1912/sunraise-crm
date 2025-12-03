<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('customer_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->index();
            $table->string('action')->nullable(); // e.g. created_project, created_lead, note_added, updated
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->text('message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            // optional FKs:
            // $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            // $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_activities');
    }
}
