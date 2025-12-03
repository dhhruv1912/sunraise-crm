<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDocumentsAddPolymorphic extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {

            // Add polymorphic fields
            $table->string('entity_type')->nullable()->after('project_id');
            $table->unsignedBigInteger('entity_id')->nullable()->after('entity_type');

            // Indexes for faster lookups
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['entity_type','entity_id']);
        });
    }
}
