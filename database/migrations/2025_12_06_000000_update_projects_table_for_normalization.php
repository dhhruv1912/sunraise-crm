<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {

            // Add new normalized FK
            if (!Schema::hasColumn('projects', 'quote_request_id')) {
                $table->unsignedBigInteger('quote_request_id')
                    ->nullable()
                    ->after('id');
            }

            // Drop old denormalized fields (check first to prevent errors on existing DBs)
            $dropColumns = [
                'customer_name',
                'mobile',
                'address',
                'latitude',
                'longitude',
                'kw',
                'module_brand',
                'inverter_brand',
                'module_count',
                'project_value',
                'aadhar_card_number',
                'aadhar_card',
                'pan_card_number',
                'pan_card',
            ];

            foreach ($dropColumns as $col) {
                if (Schema::hasColumn('projects', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {

            if (Schema::hasColumn('projects', 'quote_request_id')) {
                $table->dropColumn('quote_request_id');
            }

            // Re-add removed fields on rollback
            $table->string('customer_name')->nullable();
            $table->string('mobile')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('kw', 8, 2)->nullable();
            $table->string('module_brand')->nullable();
            $table->string('inverter_brand')->nullable();
            $table->integer('module_count')->nullable();
            $table->decimal('project_value', 12, 2)->nullable();
            $table->string('aadhar_card_number')->nullable();
            $table->string('aadhar_card')->nullable();
            $table->string('pan_card_number')->nullable();
            $table->string('pan_card')->nullable();
        });
    }
};
