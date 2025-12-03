<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDocumentsAddPolymorphic2 extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {

            // Add new polymorphic columns
            if (Schema::hasColumn('documents', 'entity_type')) {
                $table->string('entity_type', 255)->nullable()->change();
            }
        });
    }

}
