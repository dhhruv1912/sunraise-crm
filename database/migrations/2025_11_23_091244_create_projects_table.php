<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // RELATIONSHIP
            $table->unsignedBigInteger('lead_id')->nullable()->index();
            $table->string('project_code')->nullable()->unique();

            // CUSTOMER INFO
            $table->string('customer_name')->nullable();
            $table->string('mobile')->nullable();
            $table->text('address')->nullable();

            // GEO LOCATION (Suggested)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // TECHNICAL
            $table->decimal('kw', 10, 2)->nullable();
            $table->string('module_brand')->nullable();
            $table->string('inverter_brand')->nullable();
            $table->integer('module_count')->nullable();

            // WORKFLOW STATUS
            $table->enum('status', [
                'new',
                'document_collection',
                'document_registration',
                'document_verification',
                'fisibility_approval',
                'site_visit',

                'make_boq',
                'boq_approved',

                'site_dispatch',
                'installation_started',
                'project_execution',

                'project_completion',
                'quality_check',
                'inspection',
                'lisoning_after_qc',
                'self_certification',

                'meter_file',
                'meter_file_submission',
                'subsidy_claim',

                'project_invoice',
                'project_file',

                'handover',
                'complete'
            ])->default('new')->index();

            // SUGGESTED WORKFLOW DATES
            $table->date('survey_date')->nullable();
            $table->date('installation_start_date')->nullable();
            $table->date('installation_end_date')->nullable();
            $table->date('inspection_date')->nullable();
            $table->date('handover_date')->nullable();
            $table->date('estimated_complete_date')->nullable();
            $table->date('actual_complete_date')->nullable();

            // SUGGESTED WORKFLOW FILES
            $table->string('design_file')->nullable();
            $table->string('boq_file')->nullable();

            // ASSIGNMENT
            $table->unsignedBigInteger('assignee')->nullable()->index();
            $table->unsignedBigInteger('reporter')->nullable()->index();

            // FINANCE
            $table->decimal('project_value', 15, 2)->nullable();
            $table->decimal('finalize_price', 15, 2)->nullable();
            $table->decimal('emi', 15, 2)->nullable();

            // BILLING (Suggested)
            $table->enum('billing_status', ['pending','partial','paid'])->default('pending');
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('payment_received', 15, 2)->nullable();

            // SUBSIDY PROCESS
            $table->enum('subsidy_status', ['pending','applied','approved','rejected'])->default('pending');
            $table->decimal('subsidy_amount', 15, 2)->nullable();
            $table->string('subsidy_file')->nullable();

            // IDENTIFICATION
            $table->string('aadhar_card_number')->nullable();
            $table->string('aadhar_card')->nullable();
            $table->string('pan_card_number')->nullable();
            $table->string('pan_card')->nullable();

            // LIGHT BILL
            $table->string('lightbill_number')->nullable();
            $table->string('light_bill')->nullable();
            $table->string('sanction_load')->nullable();
            $table->string('service_number')->nullable();

            // BANK DETAILS
            $table->string('bank_account_number')->nullable();
            $table->string('micr_code')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ac_holder_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('cancel_cheque')->nullable();

            // PHOTO
            $table->string('passport_size_photo')->nullable();

            // MULTI DOCUMENT SUPPORT (Suggested)
            $table->json('site_photos')->nullable();
            $table->json('documents')->nullable();

            // PRIORITY
            $table->enum('priority', ['low','medium','high'])->default('medium')->index();

            // WORKFLOW ENGINE
            $table->string('current_step')->nullable();
            $table->string('next_step')->nullable();
            $table->boolean('is_on_hold')->default(false);
            $table->string('hold_reason')->nullable();

            // NOTES
            $table->longText('project_note')->nullable();

            $table->timestamps();

            // FOREIGNS
            // $table->foreign('lead_id')->references('id')->on('leads')->cascadeOnDelete();
            // $table->foreign('assignee')->references('id')->on('users')->nullOnDelete();
            // $table->foreign('reporter')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
