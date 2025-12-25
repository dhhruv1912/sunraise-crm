<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Add new field
            $table->unsignedBigInteger('lead_id')->nullable()->after('id');
            $table->dropColumn('quote_request_id');

        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('lead_id');
            $table->unsignedBigInteger('quote_request_id')->nullable()->after('id');
        });
    }
};
