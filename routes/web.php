<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\CompanyController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PanelAttachmentController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\PanelMovementController;
use App\Http\Controllers\PanelReceiveController;
use App\Http\Controllers\PanelSaleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\QuoteMasterController;
use App\Http\Controllers\QuoteRequestApiController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TellyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [PasswordController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordController::class, 'sendResetEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [PasswordController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'reset'])->name('password.update');
});

Route::middleware(['auth'])->group(function () {
    Route::any('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect('/dashboard')->with('success', 'Email verified successfully!');
    })->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
    Route::get('/company/select', [CompanyController::class, 'choosePage'])->name('company.select');
    Route::post('/company/select', [CompanyController::class, 'selectCompany'])->name('company.select.submit');

    Route::get('/dashboard/sunraise', fn () => view('page.dash.sunraise'))->name('dashboard.sunraise');
    Route::get('/dashboard/arham', fn () => view('page.dash.arham'))->name('dashboard.arham');
    Route::get('/dashboard', fn () => redirect()->route('dashboard.'.session('active_company', 'sunraise')))->name('dashboard');

    Route::get('/{module?}/setting/{id?}', [SettingsController::class, 'load'])->name('Setting');
    Route::get('/search/global', [SearchController::class, 'search'])->name('search.global');

    // Route::middleware(['auth','role:Admin'])->prefix('admin')->group(function(){

    // User roles (assign roles to user)

    Route::prefix('dashboard/sunraise/ajax')->name('dashboard.sunraise.ajax.')->group(function () {

        Route::get('/top', [DashboardController::class, 'ajaxTop'])->name('top');
        Route::get('/invoice-trend', [DashboardController::class, 'ajaxInvoiceTrend'])->name('invoice_trend');
        Route::get('/emi', [DashboardController::class, 'ajaxEmiSummary'])->name('emi');
        Route::get('/projects', [DashboardController::class, 'ajaxProjectHealth'])->name('projects');
        Route::get('/overdue', [DashboardController::class, 'ajaxOverdue'])->name('overdue');
        Route::get('/upcoming', [DashboardController::class, 'ajaxUpcoming'])->name('upcoming');
        Route::get('/workload', [DashboardController::class, 'ajaxWorkload'])->name('workload');
        Route::get('/activity', [DashboardController::class, 'ajaxActivity'])->name('activity');
        Route::get('/insights', [DashboardController::class, 'ajaxInsights'])->name('insights');
        Route::get('/ledger-monthly', [DashboardController::class, 'ajaxLedgerMonthly'])->name('ledger_monthly');
    });

    Route::prefix('users')->name('users.')->middleware('can:users.view')->group(function () {
        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [UserController::class, 'ajaxList'])->name('list');
            Route::get('/widgets', [UserController::class, 'ajaxWidgets'])->name('widgets');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::post('/update/{user}', [UserController::class, 'update'])->name('update');
            Route::post('/status/{user}', [UserController::class, 'changeStatus'])->name('status');
        });
        Route::name('view.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->middleware('can:users.view')->name('list');
            Route::get('/create', [UserController::class, 'create'])->middleware('can:users.edit')->name('create');
            Route::get('/edit/{user}', [UserController::class, 'edit'])->middleware('can:users.edit')->name('edit');
        });
    });

    Route::prefix('roles')->name('roles.')->middleware('can:users.roles')->group(function () {

        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [RoleController::class, 'ajaxList'])->name('list');
            Route::post('/store', [RoleController::class, 'store'])->name('store');
            Route::delete('/delete/{role}', [RoleController::class, 'destroy'])->name('delete');
            Route::get('/widgets', [RoleController::class, 'ajaxWidgets'])->name('widgets');
            Route::post('/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('permissions');
            Route::get('/{role}/permissions/widgets', [RoleController::class, 'permissionWidgets'])->name('permissions.widgets');
        });

        Route::name('view.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('list');
            Route::get('/{role}/permissions', [RoleController::class, 'permissions'])->name('permissions');
        });

    });

    Route::prefix('permissions')->name('permissions.')->middleware('can:users.permissions')->group(function () {

        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [PermissionController::class, 'ajaxList'])->name('list');
            Route::post('/store', [PermissionController::class, 'store'])->name('store');
            Route::delete('/delete/{permission}', [PermissionController::class, 'destroy'])->name('delete');
            Route::get('/widgets', [PermissionController::class, 'ajaxWidgets'])->name('widgets');
        });

        Route::name('view.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('list');
        });

    });

    Route::prefix('attendance')->name('attendance.')->middleware('can:users.attendance')->group(function () {

        Route::name('view.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('list');
        });
        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [AttendanceController::class, 'ajaxList'])->name('list');
            Route::get('/widgets', [AttendanceController::class, 'ajaxWidgets'])->name('widgets');
        });
        Route::get('/report/{user}', [AttendanceController::class, 'generateReport'])->name('report');
        Route::get('/salary-slip/{user}', [AttendanceController::class, 'salarySlipPdf'])->name('salary.slip');

    });

    Route::prefix('quote-master')->name('quote_master.')->middleware('can:quote.master.view')->group(function () {
        Route::name('view.')->group(function () {
            Route::get('/', [QuoteMasterController::class, 'index'])->name('list');
            Route::get('/create', [QuoteMasterController::class, 'create'])->middleware('can:quote.master.edit')->name('create');
            Route::get('/{id}/edit', [QuoteMasterController::class, 'edit'])->middleware('can:quote.master.edit')->name('edit');
        });

        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [QuoteMasterController::class, 'ajaxList'])->name('list');
            Route::get('/widgets', [QuoteMasterController::class, 'ajaxWidgets'])->name('widgets');
            Route::post('/store', [QuoteMasterController::class, 'store'])->name('store');
            Route::get('/{id}', [QuoteMasterController::class, 'getQuoteMaster'])->name('single');
            Route::post('/{id}/update', [QuoteMasterController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [QuoteMasterController::class, 'destroy'])->name('delete');
            Route::get('/chart/kw-price', [QuoteMasterController::class, 'kwPriceChart'])->name('chart.kw_price');
        });

    });

    Route::prefix('quote-requests')->name('quote_requests.')->middleware('can:quote.request.view')->group(function () {

        Route::name('view.')->group(function () {
            Route::get('/', [QuoteRequestController::class, 'index'])->name('list');
            Route::get('/create', [QuoteRequestController::class, 'create'])->middleware('can:quote.request.edit')->name('create');
            Route::get('/{id}', [QuoteRequestController::class, 'view'])->middleware('can:quote.request.edit')->name('show');
        });

        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [QuoteRequestController::class, 'ajaxList'])->name('list');
            Route::get('/widgets', [QuoteRequestController::class, 'ajaxWidgets'])->name('widgets');
            Route::post('/status/{id}', [QuoteRequestController::class, 'updateStatus'])->name('status');
            Route::get('/charts', [QuoteRequestController::class, 'ajaxChartData'])->name('charts');
            Route::post('/assign-user/{id}', [QuoteRequestController::class, 'assignUser'])->name('assign_user');
            Route::post('/update-quote-master/{id}', [QuoteRequestController::class, 'updateQuoteMaster'])->name('update_quote_master');
            Route::post('/send-email/{id}', [QuoteRequestController::class, 'sendQuoteEmail'])->name('send_email');
            Route::post('/convert-to-lead/{id}', [QuoteRequestController::class, 'convertToLead'])->name('convert_to_lead');
            Route::post('/store', [QuoteRequestController::class, 'store'])->name('store');
            Route::post('/api/create', [QuoteRequestApiController::class, 'store'])->middleware('auth:sanctum');
            Route::post('/import', [QuoteRequestController::class, 'import'])->name('import');
        });

    });

    Route::prefix('leads')->name('leads.')->middleware('can:marketing.lead.view')->group(function () {

        Route::name('view.')->group(function () {
            Route::get('/', [LeadController::class, 'index'])->name('list');
            Route::get('/{id}', [LeadController::class, 'view'])->name('show');
            Route::get('/{id}/edit', [LeadController::class, 'edit'])->middleware("can:marketing.lead.edit")->name('edit');
            Route::get('/{lead}/convert', [LeadController::class, 'preview'])->middleware("can:marketing.lead.edit")->name('convert.preview');

        });

        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [LeadController::class, 'ajaxList'])->name('list');
            Route::get('/widgets', [LeadController::class, 'ajaxWidgets'])->name('widgets');
            Route::get('/alerts', [LeadController::class, 'ajaxAlerts'])->name('alerts');
            Route::get('/charts', [LeadController::class, 'ajaxCharts'])->name('charts');
            Route::post('/update/{lead}', [LeadController::class, 'update'])->name('update');
            Route::post('/{lead}/convert', [LeadController::class, 'store'])->name('convert.store');
        });

    });

    Route::prefix('quotations')->name('quotations.')->middleware('can:quotation.view')->group(function () {

        Route::name('view.')->group(function () {
            Route::get('/', [QuotationController::class, 'index'])->name('list');
            Route::get('/{quotation}', [QuotationController::class, 'view'])->name('show');
            Route::get('/create/{lead}', [QuotationController::class, 'create'])->middleware('can:quotation.edit')->name('create');
        });
        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [QuotationController::class, 'ajaxList'])->name('list');
            Route::get('/widgets', [QuotationController::class, 'ajaxWidgets'])->name('widgets');
            Route::post('/store', [QuotationController::class, 'store'])->name('store');
            Route::get('{quotation}/generate-pdf', [QuotationController::class, 'generatePdf'])->name('generate-pdf');
            Route::post('/{quotation}/send-email', [QuotationController::class, 'sendEmail'])->name('send_email');
        });
    });

    Route::prefix('invoices')->name('invoices.')->middleware('can:billing.view')->group(function () {

        Route::name('view.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('list');
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::get('/{invoice}', [InvoiceController::class, 'view'])->name('show');
        });

        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [InvoiceController::class, 'ajaxList'])->name('list');
            Route::get('/widgets', [InvoiceController::class, 'ajaxWidgets'])->name('widgets');
            Route::get('/{invoice}/payments', [InvoiceController::class, 'ajaxPayments'])->name('payments');
            Route::post('/{invoice}', [InvoiceController::class, 'update'])->name('update');

            Route::post('/', [InvoiceController::class, 'store'])->name('store');
            Route::post('/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('payments.store');
            Route::post('/{invoice}/send', [InvoiceController::class, 'sendEmail'])->name('send');
            Route::get('/quote-master/{project}', [InvoiceController::class, 'ajaxQuoteMaster'])->name('quoteMaster');
            Route::post('/{invoice}/generate-pdf', [InvoiceController::class, 'generatePdf'])->name('generatePdf');
            Route::get('/widgets/upcoming-payments/{invoice_id?}', [InvoiceController::class, 'ajaxUpcomingPayments'])->name('upcomingPayments');
        });

    });

    Route::prefix('customers')->name('customers.')->group(function () {

        /* ================= VIEW PAGES ================= */

        Route::name('view.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('list');
            Route::get('/{customer}', [CustomerController::class, 'view'])->name('show');
            Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::get('/create', [CustomerController::class, 'create'])->name('create');
        });

        /* ================= AJAX ================= */

        Route::prefix('ajax')->name('ajax.')->group(function () {

            /* list + widgets */
            Route::get('/list', [CustomerController::class, 'ajaxList'])->name('list');
            Route::get('/widgets', [CustomerController::class, 'ajaxWidgets'])->name('widgets');
            /* customer profile sections */
            Route::get('/{customer}/activities', [CustomerController::class, 'ajaxActivities'])->name('activities');
            Route::get('/{customer}/documents', [CustomerController::class, 'ajaxDocuments'])->name('documents');
            /* create / update */
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        });

    });

    Route::prefix('documents')->name('documents.')->middleware('can:project.documents.view')->group(function () {

        Route::name('view.')->group(function () {
            Route::get('/', [DocumentController::class, 'index'])->name('list');
            Route::get('/{document}', [DocumentController::class, 'view'])->name('show');
        });

        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [DocumentController::class, 'ajaxList'])->name('list');
            Route::get('/widgets', [DocumentController::class, 'ajaxWidgets'])->name('widgets');
            Route::get('/advanced-widgets', [DocumentController::class, 'ajaxAdvancedWidgets'])->name('advanced_widgets');
            Route::get('/filters', [DocumentController::class, 'ajaxFilters'])->name('filters');
            Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
            Route::post('/upload/customer', [DocumentController::class, 'uploadCustomer'])->name('uploadCustomer');
            Route::post('/upload/project', [DocumentController::class, 'uploadProject'])->name('uploadProject');
            Route::delete('/{document}', [DocumentController::class, 'delete'])->name('delete');
        });
    });

    Route::prefix('projects')->name('projects.')->group(function () {

        Route::name('view.')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name('list');
            Route::get('/{project}', [ProjectController::class, 'view'])->name('show');
            Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
        });

        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/list', [ProjectController::class, 'ajaxList'])->name('list');
            Route::get('/widgets', [ProjectController::class, 'ajaxWidgets'])->name('widgets');
            Route::get('/{project}/dashboard', [ProjectController::class, 'ajaxDashboard'])->name('dashboard');
            Route::post('/{project}/status', [ProjectController::class, 'updateStatus'])->name('status');
            Route::post('/{project}/milestone/{key}', [ProjectController::class, 'completeMilestone'])->name('milestone.complete');
            Route::post('/{project}/emi/pay', [ProjectController::class, 'payEmi'])->name('emi.pay');
            Route::post('/{project}', [ProjectController::class, 'update'])->name('update');
        });

        Route::prefix('{project}/ajax')->name('ajax.')->group(function () {
            Route::get('/widgets', [ProjectController::class, 'ajaxWid']);
            Route::get('/status', [ProjectController::class, 'ajaxStatus']);
            Route::get('/timeline', [ProjectController::class, 'ajaxTimeline']);
            Route::get('/billing', [ProjectController::class, 'ajaxBilling']);
            Route::get('/emi', [ProjectController::class, 'ajaxEmi']);
            Route::get('/documents', [ProjectController::class, 'ajaxDocuments']);
            Route::get('/activities', [ProjectController::class, 'ajaxActivities']);
        });
    });

    Route::prefix('reports')->name('reports.')->group(function () {

        Route::get('/', [ReportController::class, 'index'])->name('index');

        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/execution', [ReportController::class, 'execution'])->name('execution');
            Route::get('/delays', [ReportController::class, 'delays'])->name('delays');
            Route::get('/cashflow', [ReportController::class, 'cashflow'])->name('cashflow');
            Route::get('/workload', [ReportController::class, 'workload'])->name('workload');
        });

    });

    // Route::prefix('settings')->name('settings.')->group(function () {

    //     Route::get('/', [SettingsController::class, 'index'])->name('index');

    //     Route::prefix('ajax')->name('ajax.')->group(function () {
    //         Route::post('/save', [SettingsController::class, 'save'])->name('save');
    //     });

    // });
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/{module}', [SettingsController::class, 'module'])->name('module');

        Route::prefix('ajax')->name('ajax.')->group(function () {
            // Route::get('/{group}', [SettingsController::class, 'ajaxGroup'])->name('group');
            Route::get('/list/{group?}', [SettingsController::class, 'ajaxList'])->name('list');
            Route::post('/save', [SettingsController::class, 'save'])->name('save');

            Route::post('/create', [SettingsController::class, 'store']);
            Route::post('/{setting}/update', [SettingsController::class, 'update']);
            Route::delete('/{setting}', [SettingsController::class, 'destroy']);
        });
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->name('index');
        Route::get('/settings', [UserSettingController::class, 'index'])->name('settings');

        Route::prefix('ajax')->name('ajax.')->group(function () {
            Route::get('/settings', [UserSettingController::class, 'ajaxSettings'])->name('settings');
            Route::post('/settings/save', [UserSettingController::class, 'save'])->name('settings.save');
            Route::post('/settings/reset', [UserSettingController::class, 'resetToDefault'])->name('settings.reset');
            Route::get('/basic-info', [UserController::class, 'ajaxBasicInfo'])->name('basic-info');
            Route::get('/assignments', [UserController::class, 'ajaxAssignments'])->name('assignments');
            Route::get('/timeline', [UserController::class, 'ajaxTimeline'])->name('timeline');
        });

    });

    Route::middleware('company:sunraise')->group(function () {
        Route::get('/ajax/projects/search', [DocumentController::class, 'searchProjects'])->name('search.projects');

    });

    Route::middleware(['company:arham'])->group(function () {
        Route::prefix('vendors')->name('vendors.')->group(function () {
            Route::get('/', [VendorController::class, 'index'])->name('index');
            Route::get('/list', [VendorController::class, 'list'])->name('list');
            Route::post('/store', [VendorController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [VendorController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [VendorController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [VendorController::class, 'delete'])->name('delete');
        });
        Route::prefix('warehouse')->name('warehouse.')->group(function () {
            Route::get('/', [WarehouseController::class, 'index'])->name('index');
            Route::get('/list', [WarehouseController::class, 'list'])->name('list');
            Route::post('/store', [WarehouseController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [WarehouseController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [WarehouseController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [WarehouseController::class, 'delete'])->name('delete');
        });

        Route::prefix('item-categories')->name('item_categories.')->group(function () {
            Route::get('/', [ItemCategoryController::class, 'index'])->name('index');
            Route::get('/list', [ItemCategoryController::class, 'list'])->name('list');
            Route::post('/store', [ItemCategoryController::class, 'store'])->name('store');
            Route::get('/{id}', [ItemCategoryController::class, 'edit'])->name('edit');
            Route::post('/{id}/update', [ItemCategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [ItemCategoryController::class, 'delete'])->name('delete');
        });

        Route::prefix('batches')->name('batches.')->group(function () {
            Route::get('/', [BatchController::class, 'index'])->name('index');
            Route::get('/list', [BatchController::class, 'list'])->name('list');
            Route::get('/create', [BatchController::class, 'create'])->name('create');
            Route::post('/review', [BatchController::class, 'review'])->name('review');
            Route::get('/review', [BatchController::class, 'reviewGet'])->name('reviewget');
            Route::post('/store', [BatchController::class, 'store'])->name('store');
            Route::get('/{id}', [BatchController::class, 'show'])->name('show');
            Route::post('/{id}/update', [BatchController::class, 'update'])->name('update');
            Route::get('/{id}/download-original', [BatchController::class, 'downloadOriginal'])->name('download_original');
            Route::get('/{id}/download-generated', [BatchController::class, 'downloadGenerated'])->name('download_generated');
            Route::post('/{id}/regenerate-pdf', [BatchController::class, 'regeneratePDF'])->name('regenerate_pdf');
        });

        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [ItemController::class, 'index'])->name('index');
            Route::get('/list', [ItemController::class, 'list'])->name('list');
            Route::post('/store', [ItemController::class, 'store'])->name('store');
            Route::get('/{id}', [ItemController::class, 'edit'])->name('edit');
            Route::post('/{id}/update', [ItemController::class, 'update'])->name('update');
            Route::delete('/{id}', [ItemController::class, 'delete'])->name('delete');
        });

        /* -----------------------------------------------
        | PANEL LIST & DETAILS
        |-----------------------------------------------*/
        Route::prefix('panels')->name('panels.')->group(function () {
            Route::prefix('receive')->name('receive.')->group(function () {
                Route::get('/upload', [PanelReceiveController::class, 'uploadPage'])->name('uploadPage');
                Route::post('/upload', [PanelReceiveController::class, 'uploadInvoice'])->name('uploadInvoice');
                Route::get('/confirm', [PanelReceiveController::class, 'confirmPage'])->name('confirm');
                Route::post('/save', [PanelReceiveController::class, 'savePanels'])->name('savePanels');
            });
            Route::prefix('attachment')->name('attachment.')->group(function () {
                Route::get('/{id}/download', [PanelAttachmentController::class, 'download'])->name('download');
                Route::post('/{id}/ocr', [PanelAttachmentController::class, 'updateOCR'])->name('updateOCR');
                Route::post('/{id}/generate-pdf', [PanelAttachmentController::class, 'generatePDF'])->name('generatePDF');
            });
            Route::get('/', [PanelController::class, 'index'])->name('index');
            Route::get('/list', [PanelController::class, 'list'])->name('list'); // AJAX
            Route::get('/show/{id}', [PanelController::class, 'show'])->name('show'); // modal
            Route::delete('/{id}', [PanelController::class, 'delete'])->name('delete');
            Route::post('/move', [PanelMovementController::class, 'move'])->name('move'); // AJAX
            Route::get('/movement/{id}', [PanelMovementController::class, 'history'])->name('movement.history'); // AJAX
            Route::post('/sell', [PanelSaleController::class, 'sell'])->name('sell'); // AJAX
            Route::post('/return', [PanelSaleController::class, 'return'])->name('return'); // AJAX
        });
    });

    Route::prefix('tally')->name('tally.')->group(function () {
        Route::get('/test', [TellyController::class, 'test'])->name('test');
        Route::get('/', fn () => view('page.tally.dashboard'))->name('dashboard');
        Route::get('/ledger', [TellyController::class, 'ledger'])->name('ledger');
        Route::get('/stocks', [TellyController::class, 'stocks'])->name('stocks');
        Route::prefix('data')->name('data.')->group(function () {
            Route::get('/stocks', [TellyController::class, 'loadStocks'])->name('stocks');
            Route::get('/ledger', [TellyController::class, 'loadLedger'])->name('ledger');
            Route::get('/ledger_voucher', [TellyController::class, 'loadLedgerVouchers'])->name('ledger_voucher');
            Route::get('/stock_voucher', [TellyController::class, 'loadStockVouchers'])->name('stock_voucher');
            Route::get('/balance-sheet', [TellyController::class, 'balance_sheet'])->name('balance-sheet');
            Route::get('/trial-balance', [TellyController::class, 'trial_balance'])->name('trial-balance');
            Route::get('/single-cashflow', [TellyController::class, 'single_cashflow'])->name('single-cashflow');
            Route::get('/cashflow', [TellyController::class, 'cashflow'])->name('cashflow');
        });
        Route::get('/reports/ledger-monthly', fn () => view('page.tally.reports.ledger'))->name('reports.monthly');
        Route::get('/reports/stock-monthly', fn () => view('page.tally.reports.stock'))->name('reports.stock.monthly');

    });
});

Route::get('/', function () {
    return view('welcome');
});
