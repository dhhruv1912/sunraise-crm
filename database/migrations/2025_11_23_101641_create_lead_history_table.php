<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('lead_history', function (Blueprint $table) {
            $table->id();

            // Lead reference (NO foreign key)
            $table->unsignedBigInteger('lead_id')->index();

            // User who updated (NO foreign key)
            $table->unsignedBigInteger('changed_by')->nullable()->index();

            // Event type: status_update, assigned_update, note_update, followup_update
            $table->string('action')->index();

            // Old & new values stored as JSON for flexibility
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();

            // Human readable text for feed/timeline
            $table->text('message')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_history');
    }
}
