<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->index();
            $table->string('type')->nullable(); // invoice, permit, layout, photos, etc
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            // $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            // $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
