<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up()
    {
        /*===========================================
        = 1. Add missing customer_id to projects
        ===========================================*/
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('lead_id')->index();
            }
        });

        // Backfill from leads → customers
        try {
            DB::statement("
                UPDATE projects p
                JOIN leads l ON p.lead_id = l.id
                SET p.customer_id = l.customer_id
                WHERE p.customer_id IS NULL AND l.customer_id IS NOT NULL
            ");
        } catch (\Throwable $e) {}

        /*===========================================
        = 2. Add missing customer_id foreign key to leads
        ===========================================*/
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->change();
                $table->index('customer_id');
            }
        });

        /*===========================================
        = 3. Add proper FKs to leads, projects, invoices
        ===========================================*/
        Schema::table('leads', function (Blueprint $table) {
            // quote_request
            if (!Schema::hasColumn('leads', 'quote_request_id')) return;
        });

        // ADD FKs (safe inside try/catch)
        try {
            DB::statement("ALTER TABLE leads
                ADD CONSTRAINT fk_leads_quote_request
                FOREIGN KEY (quote_request_id) REFERENCES quote_requests(id)
                ON DELETE SET NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE leads
                ADD CONSTRAINT fk_leads_customer
                FOREIGN KEY (customer_id) REFERENCES customers(id)
                ON DELETE SET NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE projects
                ADD CONSTRAINT fk_projects_customer
                FOREIGN KEY (customer_id) REFERENCES customers(id)
                ON DELETE SET NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE projects
                ADD CONSTRAINT fk_projects_lead
                FOREIGN KEY (lead_id) REFERENCES leads(id)
                ON DELETE SET NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE invoices
                ADD CONSTRAINT fk_invoices_project
                FOREIGN KEY (project_id) REFERENCES projects(id)
                ON DELETE SET NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE invoices
                ADD CONSTRAINT fk_invoices_customer
                FOREIGN KEY (customer_id) REFERENCES customers(id)
                ON DELETE SET NULL");
        } catch (\Throwable $e) {}

        /*===========================================
        = 4. Normalize documents polymorphic system
        ===========================================*/
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'entity_type')) {
                $table->string('entity_type')->default('project')->after('project_id')->index();
            }
            if (!Schema::hasColumn('documents', 'entity_id')) {
                $table->unsignedBigInteger('entity_id')->nullable()->after('entity_type')->index();
            }
        });

        // Backfill from legacy project_id
        DB::statement("
            UPDATE documents
            SET entity_type='project', entity_id=project_id
            WHERE entity_id IS NULL AND project_id IS NOT NULL
        ");
    }

    public function down()
    {
        // SAFE rollback
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'customer_id')) $table->dropColumn('customer_id');
        });

        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'entity_type')) $table->dropColumn('entity_type');
            if (Schema::hasColumn('documents', 'entity_id')) $table->dropColumn('entity_id');
        });

        // Foreign keys auto-dropped if exist
    }
};
