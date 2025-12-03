<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quote_request_id')->nullable()->index();
            $table->string('lead_code')->nullable()->unique();
            $table->unsignedBigInteger('assigned_to')->nullable()->index(); // marketing user

            $table->enum('status', [
                'new',
                'contacted',
                'site_visit_planned',
                'site_visited',
                'follow_up',
                'negotiation',
                'converted',
                'dropped'
            ])->default('new')->index();

            $table->timestamp('next_followup_at')->nullable();
            $table->text('remarks')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();

            // $table->foreign('quote_request_id')->references('id')->on('quote_requests')->cascadeOnDelete();
            // $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            // $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
