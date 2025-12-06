<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\CompanyController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MarketingLogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectLogController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\QuoteMasterController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SessionLogsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\TellyController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\InvoiceController;
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

    Route::get('/{module?}/setting', [SettingsController::class, 'load'])->name('Setting');
    Route::get('/search/global', [SearchController::class, 'search'])->name('search.global');

    // Route::middleware(['auth','role:Admin'])->prefix('admin')->group(function(){

    // User roles (assign roles to user)
    Route::get('/user/assign/{user}', [UserRoleController::class, 'edit'])->name('users.assign');
    Route::post('/user/assign/{user}', [UserRoleController::class, 'update'])->name('users.assign.update');

    Route::group(['prefix' => 'user'], function () {

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::post('/roles/{role}/update', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.delete');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::post('/permissions/{permission}/update', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.delete');

        Route::group(['prefix' => 'attendance'], function () {
            Route::get('/list', [AttendanceController::class,'list'])->name("attendance.log");
            Route::get('/load', [AttendanceController::class,'load'])->name("attendance.load");
            Route::get('/{user}/report', [AttendanceController::class,'generate_report'])->name("attendance.report");
            Route::get('/download', [AttendanceController::class,'download'])->name("attendance.download");
            Route::post('/upload', [AttendanceController::class,'upload'])->name("attendance.upload");
        });
        Route::get('/', [UserController::class, 'listPage'])->name('Users'); // ->middleware(['auth','permission:view users'])
        Route::get('/logs', [UserController::class, 'logPage'])->name('UserLogs');
        Route::get('/profile', [UserController::class, 'profilePage'])->name('Profile');
        Route::get('/export', [UserController::class, 'exportExcel'])->name('UserExcelDownload');
        Route::get('/', [UserController::class, 'index'])->name('Users');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.delete');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');

        // Assign roles (page + JSON + update)
        Route::get('/{user}/assign', [UserRoleController::class, 'edit'])->name('users.assign');
        Route::get('/{user}/roles-json', [UserRoleController::class, 'rolesJson'])->name('users.roles.json');
        Route::post('/{user}/assign', [UserRoleController::class, 'update'])->name('users.assign.update');
    });

    // Quote Master
    Route::prefix('quote')->group(function () {
        Route::get('/', [QuoteMasterController::class, 'index'])->name('quote_master.index');
        Route::get('/ajax', [QuoteMasterController::class, 'ajaxList'])->name('quote_master.ajax');
        Route::get('/create', [QuoteMasterController::class, 'create'])->name('quote_master.create');
        Route::post('/store', [QuoteMasterController::class, 'store'])->name('quote_master.store');
        Route::get('/edit/{id}', [QuoteMasterController::class, 'edit'])->name('quote_master.edit');
        Route::post('/update/{id}', [QuoteMasterController::class, 'update'])->name('quote_master.update');
        Route::post('/delete', [QuoteMasterController::class, 'delete'])->name('quote_master.delete');

        Route::get('/export', [QuoteMasterController::class, 'export'])->name('quote_master.export');
        Route::post('/import', [QuoteMasterController::class, 'import'])->name('quote_master.import');
        Route::prefix('master')->group(function () {
            Route::get('/', [QuoteMasterController::class, 'index'])->name('quote_master.index');
            Route::get('/ajax', [QuoteMasterController::class, 'ajaxList'])->name('quote_master.ajax');
            Route::get('/create', [QuoteMasterController::class, 'create'])->name('quote_master.create');
            Route::post('/store', [QuoteMasterController::class, 'store'])->name('quote_master.store');
            Route::get('/edit/{id}', [QuoteMasterController::class, 'edit'])->name('quote_master.edit');
            Route::post('/update/{id}', [QuoteMasterController::class, 'update'])->name('quote_master.update');
            Route::post('/delete', [QuoteMasterController::class, 'delete'])->name('quote_master.delete');

            Route::get('/export', [QuoteMasterController::class, 'export'])->name('quote_master.export');
            Route::post('/import', [QuoteMasterController::class, 'import'])->name('quote_master.import');
        });

        Route::prefix('requests')->group(function () {
            Route::get('/', [QuoteRequestController::class, 'index'])->name('quote_requests.index');
            Route::get('/ajax', [QuoteRequestController::class, 'ajaxList']);
            Route::get('/create', [QuoteRequestController::class, 'create'])->name('quote_requests.create');
            Route::post('/', [QuoteRequestController::class, 'store'])->name('quote_requests.store');
            Route::post('/delete', [QuoteRequestController::class, 'delete'])->name('quote_requests.delete');
            Route::get('/api/view/{id}', [QuoteRequestController::class, 'apiView'])->name('quote_requests.apiView');
            Route::get('/{id}/edit', [QuoteRequestController::class, 'edit'])->name('quote_requests.edit');
            Route::post('/{id}', [QuoteRequestController::class, 'update'])->name('quote_requests.update');

            Route::get('/{id}/view', [QuoteRequestController::class, 'view'])->name('quote_requests.view');
            Route::get('/{id}/view-json', [QuoteRequestController::class, 'viewJson'])->name('quote_requests.view.json');

            Route::post('/{id}/status', [QuoteRequestController::class, 'updateStatus'])->name('quote_requests.status');
            Route::post('/{id}/quote-master', [QuoteRequestController::class, 'updateQuoteMaster'])->name('quote_requests.quote_master');
            Route::post('/{id}/assign', [QuoteRequestController::class, 'assign'])->name('quote_requests.assign');
            Route::post('/{id}/send-mail', [QuoteRequestController::class, 'sendMail'])->name('quote_requests.send');
            Route::post('/{id}/convert-to-lead', [QuoteRequestController::class, 'createLeadIfMissing'])->name('quote_requests.convert');
            // Route::post('/{id}/convert-to-lead', function ($id) {
            //     // simple closure that calls controller helper
            //     return app(\App\Http\Controllers\QuoteRequestController::class)->createLeadIfMissing(\App\Models\QuoteRequest::findOrFail($id))
            //         ? response()->json(['status' => true, 'message' => 'Converted'])
            //         : response()->json(['status' => false, 'message' => 'Failed'], 500);
            // });
            Route::get('/export', [QuoteRequestController::class, 'export'])->name('quote_requests.export');
            Route::post('/import', [QuoteRequestController::class, 'import'])->name('quote_requests.import');
        });

        // Route::prefix('requests')->group(function () {
        //     Route::get('/', [QuoteRequestController::class,'index'])->name('quote_requests.index');
        //     Route::get('ajax-list', [QuoteRequestController::class,'ajaxList'])->name('quote_requests.ajax');
        //     Route::post('/', [QuoteRequestController::class,'store'])->name('quote_requests.store');
        //     Route::get('{id}/view-json', [QuoteRequestController::class,'viewJson'])->name('quote_requests.view_json');
        //     Route::post('{id}/assign', [QuoteRequestController::class,'assign'])->name('quote_requests.assign');
        //     Route::post('{id}/status', [QuoteRequestController::class,'updateStatus'])->name('quote_requests.status');
        //     Route::post('{id}/send-mail', [QuoteRequestController::class,'sendMail'])->name('quote_requests.send_mail');
        //     // ... other routes
        // });
    });

    Route::prefix('quotations')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('/ajax', [QuotationController::class, 'ajaxList'])->name('quotations.ajax');

        Route::get('/create', [QuotationController::class, 'create'])->name('quotations.create');
        Route::post('/save', [QuotationController::class, 'store'])->name('quotations.store');

        Route::get('/{id}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
        Route::put('/{id}', [QuotationController::class, 'update'])->name('quotations.update');

        Route::delete('/{id}', [QuotationController::class, 'destroy'])->name('quotations.destroy');

        Route::get('/{id}/generate-pdf', [QuotationController::class, 'generatePdf'])->name('quotations.generate_pdf');
        Route::get('/{id}/download', [QuotationController::class, 'downloadPdf'])->name('quotations.download');
        Route::post('/{id}/send-email', [QuotationController::class, 'sendEmail'])->name('quotations.send_email');

        Route::get('/export', [QuotationController::class, 'export'])->name('quotations.export');

    });

    Route::prefix('marketing')->group(function () {
        Route::get('/', [LeadController::class, 'index'])->name('marketing.index');
        Route::get('/ajax', [LeadController::class, 'ajaxList'])->name('marketing.ajax');
        Route::get('/create', [LeadController::class, 'create'])->name('marketing.create');
        Route::post('/store', [LeadController::class, 'store'])->name('marketing.store');
        Route::post('/delete', [LeadController::class, 'delete'])->name('marketing.delete');
        Route::get('/export', [LeadController::class, 'export'])->name('marketing.export');
        Route::post('/import', [LeadController::class, 'import'])->name('marketing.import');

        // Route::get('kanban', [\App\Http\Controllers\LeadController::class, 'kanban'])->name('leads.kanban');
        // Route::post('{lead}/move', [\App\Http\Controllers\LeadController::class, 'move'])->name('leads.move');
        Route::get('/{id}/view', [LeadController::class, 'view'])->name('marketing.view');
        Route::get('/{id}/view-json', [LeadController::class, 'viewJson'])->name('marketing.view.json');
        Route::get('/{id}/edit', [LeadController::class, 'edit'])->name('marketing.edit');
        Route::post('/{id}/update', [LeadController::class, 'update'])->name('marketing.update');
        Route::post('/{id}/status', [LeadController::class, 'updateStatus'])->name('marketing.status');
        Route::post('/{id}/assign', [LeadController::class, 'assign'])->name('marketing.assign');
        Route::post('/{id}/create-project', [LeadController::class, 'createProjectFromLead'])->name('lead.createProject');

    });

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/ajax', [DocumentController::class, 'ajaxList'])->name('ajax');
        Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
        Route::get('/view/{id}', [DocumentController::class, 'view'])->name('view');
        Route::get('/download/{id}', [DocumentController::class, 'download'])->name('download');
        Route::delete('/delete/{id}', [DocumentController::class, 'delete'])->name('delete');
        Route::post('/attach/{id}', [DocumentController::class, 'attachToProject'])->name('attach');
        Route::post('/detach/{id}', [DocumentController::class, 'detach'])->name('detach');
        Route::get('/export', [DocumentController::class, 'export'])->name('export');
    });

    Route::get('/ajax/projects/search', [DocumentController::class, 'searchProjects'])->name('search.projects');
    
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/ajax', [CustomerController::class, 'ajax'])->name('customers.ajax');
        Route::get('/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/store', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::post('/update/{id}', [CustomerController::class, 'update'])->name('customers.update');
        Route::post('/delete', [CustomerController::class, 'delete'])->name('customers.delete');

        // JSON modal
        Route::get('/view-json/{id}', [CustomerController::class, 'viewJson']);

        // Global search for Lead/Project linking
        Route::get('/search', [CustomerController::class, 'searchApi']);
    });

    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/ajax', [ProjectController::class, 'ajaxList'])->name('projects.ajax');
        Route::get('/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/store', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::post('/{id}/update', [ProjectController::class, 'update'])->name('projects.update');
        Route::get('/{id}/view', [ProjectController::class, 'view'])->name('projects.view'); // html view page
        Route::get('/{id}/view-json', [ProjectController::class, 'viewJson'])->name('projects.view.json'); // ajax json for modal
        Route::post('/{id}/assign', [ProjectController::class, 'assign'])->name('projects.assign');
        Route::post('/{id}/status', [ProjectController::class, 'changeStatus'])->name('projects.status');
        Route::post('/{id}/delete', [ProjectController::class, 'delete'])->name('projects.delete');
        Route::post('/{id}/attach-document', [ProjectController::class, 'attachDocument'])->name('projects.attach_document');
        Route::get('/{id}/history', [ProjectController::class, 'history'])->name('projects.history.json'); // returns json
    });

    Route::prefix('billing')->group(function () {
        Route::get('/invoices', [InvoiceController::class,'index'])->name('invoices.index');
        Route::get('/invoices/ajax', [InvoiceController::class,'ajaxList'])->name('invoices.ajax');
        Route::get('/invoices/create', [InvoiceController::class,'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class,'store'])->name('invoices.store');
        Route::get('/invoices/{id}', [InvoiceController::class,'show'])->name('invoices.show');
        Route::get('/invoices/{id}/view-json', [InvoiceController::class,'viewJson'])->name('invoices.show');
        Route::get('/invoices/{id}/edit', [InvoiceController::class,'edit'])->name('invoices.edit');
        Route::post('/invoices/{id}', [InvoiceController::class,'update'])->name('invoices.update');
        Route::delete('/invoices/{id}', [InvoiceController::class,'destroy'])->name('invoices.destroy');

        Route::post('/invoices/{id}/payments', [InvoiceController::class,'recordPayment'])->name('invoices.payments.store');
        Route::get('/invoices/{id}/pdf', [InvoiceController::class,'generatePdf'])->name('invoices.pdf');
        Route::post('/invoices/{id}/send', [InvoiceController::class,'sendEmail'])->name('invoices.send');
        Route::get('/invoices/export', [InvoiceController::class,'export'])->name('invoices.export');
        Route::get('/sku/{id}', [InvoiceController::class,'sku'])->name('invoices.sku');

    });


    Route::prefix('tally')->name('tally.')->group(function () {
        Route::get('/', [TellyController::class, 'ledger'])->name("index");
        Route::get('/ledger', [TellyController::class, 'ledger'])->name("ledger");
        Route::get('/stocks', [TellyController::class, 'stocks'])->name("stocks");
        Route::prefix('data')->name('data.')->group(function () {
            Route::get('/stocks', [TellyController::class, 'loadStocks'])->name("stocks");
            Route::get('/ledger', [TellyController::class, 'loadLedger'])->name("ledger");
            Route::get('/ledger_voucher', [TellyController::class, 'loadLedgerVouchers'])->name("ledger_voucher");
            Route::get('/stock_voucher', [TellyController::class, 'loadStockVouchers'])->name("stock_voucher");
        });

    });

    // Route::get('/{module?}/setting', [SettingsController::class, 'index'])->name('settings.index');
    Route::prefix('settings')->group(function () {
        Route::get('/{module}', [SettingsController::class, 'load'])->name('SettingsModule');
        Route::get('/get/{name}', [SettingsController::class, 'get']);
        Route::post('/save', [SettingsController::class, 'save']);
        Route::post('/save-value/{name}', [SettingsController::class, 'saveValue']);
        Route::delete('/delete/{id}', [SettingsController::class, 'delete']);
        Route::post('/reorder', [SettingsController::class, 'reorder']);
        Route::get('/export', [SettingsController::class, 'export'])->name('SettingsExport');
        Route::post('/import', [SettingsController::class, 'import'])->name('SettingsImport');
        // Route::get('/load/{module}', [SettingsController::class, 'load'])->name('settings.load');
        // Route::get('/get/{name}', [SettingsController::class, 'get'])->name('settings.get');
        // Route::post('/save', [SettingsController::class, 'save'])->name('settings.save');
        // Route::post('/save-value/{name}', [SettingsController::class, 'saveValue'])->name('settings.saveValue');
        // Route::delete('/delete/{id}', [SettingsController::class, 'delete'])->name('settings.delete');
        // Route::post('/reorder', [SettingsController::class, 'reorder'])->name('settings.reorder');
        // Route::get('/export', [SettingsController::class, 'export'])->name('settings.export');
        // Route::post('/import', [SettingsController::class, 'import'])->name('settings.import');
    });

});

Route::get('/', function () {
    return view('welcome');
});
