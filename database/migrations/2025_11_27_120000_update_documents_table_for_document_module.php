<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDocumentsTableForDocumentModule extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {

            // Add new fields only if missing (safe migration)
            if (!Schema::hasColumn('documents', 'file_name')) {
                $table->string('file_name')->nullable()->after('type');
            }

            if (!Schema::hasColumn('documents', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('file_path');
            }

            if (!Schema::hasColumn('documents', 'size')) {
                $table->unsignedBigInteger('size')->nullable()->after('mime_type');
            }

            if (!Schema::hasColumn('documents', 'description')) {
                $table->text('description')->nullable()->after('size');
            }

            if (!Schema::hasColumn('documents', 'tags')) {
                $table->json('tags')->nullable()->after('description');
            }

            // Your existing project_id is required — change to nullable for global document support
            if (Schema::hasColumn('documents', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->change();
            }

            // uploaded_by already exists but ensure nullable
            if (Schema::hasColumn('documents', 'uploaded_by')) {
                $table->unsignedBigInteger('uploaded_by')->nullable()->change();
            }

            // file_path already exists → ensure consistent naming
            // your old migration already used `file_path`, so no change required

            // meta already exists → keep as json
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Remove added fields — but only if they exist
            if (Schema::hasColumn('documents', 'file_name')) {
                $table->dropColumn('file_name');
            }

            if (Schema::hasColumn('documents', 'mime_type')) {
                $table->dropColumn('mime_type');
            }

            if (Schema::hasColumn('documents', 'size')) {
                $table->dropColumn('size');
            }

            if (Schema::hasColumn('documents', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('documents', 'tags')) {
                $table->dropColumn('tags');
            }

            // revert project_id to NOT NULL if you want (optional)
            // $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });
    }
}
