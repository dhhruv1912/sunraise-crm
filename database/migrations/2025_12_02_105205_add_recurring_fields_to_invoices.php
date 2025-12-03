<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurringFieldsToInvoices extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('notes');
            $table->enum('recurring_type', ['daily','weekly','monthly','yearly','custom'])->nullable();
            $table->integer('recurring_interval')->nullable()->comment('every N units');
            $table->date('recurring_next_at')->nullable();
            $table->date('recurring_end_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'is_recurring', 'recurring_type', 'recurring_interval',
                'recurring_next_at', 'recurring_end_at'
            ]);
        });
    }
}
