<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quote_requests', function (Blueprint $table) {

            $table->unsignedBigInteger('customer_id')
                  ->nullable()
                  ->after('id');

            $table->index('customer_id');
            
            if (Schema::hasColumn('quote_requests', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('quote_requests', 'number')) {
                $table->dropColumn('number');
            }
            if (Schema::hasColumn('quote_requests', 'email')) {
                $table->dropColumn('email');
            }
        });
    }

    public function down()
    {
        Schema::table('quote_requests', function (Blueprint $table) {

            // Reverse: remove customer_id
            if (Schema::hasColumn('quote_requests', 'customer_id')) {
                // If FK was added:
                // $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }

            // Restore dropped fields
            $table->string('name')->nullable();
            $table->string('number')->nullable();
            $table->string('email')->nullable();
        });
    }
};