<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInvoicesForProjectOnly extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'lead_id')) {
                $table->dropColumn('lead_id');
            }

            if (!Schema::hasColumn('invoices', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->index();
            }
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'lead_id')) {
                $table->unsignedBigInteger('lead_id')->nullable()->index();
            }

            if (Schema::hasColumn('invoices', 'project_id')) {
                $table->dropColumn('project_id');
            }
        });
    }
}
