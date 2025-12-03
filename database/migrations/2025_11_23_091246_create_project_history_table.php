<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('project_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->index();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            // $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            // $table->foreign('changed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_history');
    }
}
