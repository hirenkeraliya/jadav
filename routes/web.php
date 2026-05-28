<?php

use App\Http\Controllers\Auth\CompanySelectController;
use App\Http\Controllers\Auth\ImpersonateController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\PayrollEntryController;
use App\Http\Controllers\PayrollReportController;
use App\Http\Controllers\ProjectCompletionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectVariationController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Guest routes ──────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');
});

// ── Auth routes (no company required) ─────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('company/select', [CompanySelectController::class, 'show'])->name('company.select');
    Route::post('company/select', [CompanySelectController::class, 'select'])->name('company.select.post');

    Route::get('password/change', [PasswordController::class, 'showChange'])->name('password.change');
    Route::post('password/change', [PasswordController::class, 'change'])->name('password.change.post');
});

// ── Impersonation ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'super.admin'])->group(function () {
    Route::post('impersonate/{user}', [ImpersonateController::class, 'impersonate'])->name('impersonate');
});
Route::middleware('auth')->post('impersonate/leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');

// ── Authenticated + company-scoped routes ─────────────────────────────────────
Route::middleware(['auth', 'company.selected'])->group(function () {

    Route::post('company/switch', [CompanySelectController::class, 'switch'])->name('company.switch');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Customers
    Route::post('customers/quick-store', [CustomerController::class, 'quickStore'])->name('customers.quick-store');
    Route::resource('customers', CustomerController::class);

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::patch('projects/{project}/status', [ProjectController::class, 'changeStatus'])->name('projects.change-status');
    Route::patch('projects/{project}/work', [ProjectController::class, 'updateWork'])->name('projects.update-work');
    Route::get('projects/{project}/pdf', [ProjectController::class, 'pdf'])->name('projects.pdf');
    // Project Completion (invoice + payment tracking)
    Route::get('projects/{project}/complete',                    [ProjectCompletionController::class, 'create'])->name('projects.complete.create');
    Route::post('projects/{project}/complete',                   [ProjectCompletionController::class, 'store'])->name('projects.complete.store');
    Route::get('projects/{project}/completion/edit',             [ProjectCompletionController::class, 'edit'])->name('projects.completion.edit');
    Route::put('projects/{project}/completion',                  [ProjectCompletionController::class, 'update'])->name('projects.completion.update');
    Route::post('projects/{project}/completion/payment',         [ProjectCompletionController::class, 'storePayment'])->name('projects.completion.payment');
    Route::delete('projects/{project}/completion/payment/{payment}', [ProjectCompletionController::class, 'destroyPayment'])->name('projects.completion.payment.destroy');
    Route::get('projects/{project}/completion/pdf',              [ProjectCompletionController::class, 'pdf'])->name('projects.completion.pdf');
    Route::post('projects/{project}/files', [ProjectController::class, 'uploadFile'])->name('projects.files.upload');
    Route::delete('projects/{project}/files/{file}', [ProjectController::class, 'deleteFile'])->name('projects.files.delete');

    // Project Variations (Extra / Less Work)
    Route::post('projects/{project}/variations', [ProjectVariationController::class, 'store'])->name('variations.store');
    Route::put('projects/{project}/variations/{variation}', [ProjectVariationController::class, 'update'])->name('variations.update');
    Route::delete('projects/{project}/variations/{variation}', [ProjectVariationController::class, 'destroy'])->name('variations.destroy');

    // Finance
    Route::get('projects/{project}/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('projects/{project}/finance/create', [FinanceController::class, 'create'])->name('finance.create');
    Route::post('projects/{project}/finance', [FinanceController::class, 'store'])->name('finance.store');
    Route::get('projects/{project}/finance/{entry}/edit', [FinanceController::class, 'edit'])->name('finance.edit');
    Route::put('projects/{project}/finance/{entry}', [FinanceController::class, 'update'])->name('finance.update');
    Route::delete('projects/{project}/finance/{entry}', [FinanceController::class, 'destroy'])->name('finance.destroy');

    // Tasks
    Route::get('projects/{project}/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('projects/{project}/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('projects/{project}/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('my-tasks', [TaskController::class, 'myTasks'])->name('tasks.mine');

    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/revise', [QuotationController::class, 'revise'])->name('quotations.revise');
    Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convertToProject'])->name('quotations.convert');
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/finance', [ReportController::class, 'finance'])->name('reports.finance');
    Route::get('reports/quotations', [ReportController::class, 'quotations'])->name('reports.quotations');
    Route::get('reports/projects', [ReportController::class, 'projects'])->name('reports.projects');

    // Payroll
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::resource('staff', StaffController::class)->names('staff');
        Route::resource('entries', PayrollEntryController::class)->names('entries');
        Route::get('report', [PayrollReportController::class, 'index'])->name('report.index');
    });

    // Settings (super admin or admin role)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('company', [SettingsController::class, 'showCompany'])->name('company');
        Route::put('company', [SettingsController::class, 'updateCompany'])->name('company.update');

        Route::get('project-types', [SettingsController::class, 'projectTypes'])->name('project-types');
        Route::post('project-types', [SettingsController::class, 'storeProjectType'])->name('project-types.store');
        Route::put('project-types/{projectType}', [SettingsController::class, 'updateProjectType'])->name('project-types.update');
        Route::delete('project-types/{projectType}', [SettingsController::class, 'destroyProjectType'])->name('project-types.destroy');

        Route::get('payment-types', [SettingsController::class, 'paymentTypes'])->name('payment-types');
        Route::post('payment-types', [SettingsController::class, 'storePaymentType'])->name('payment-types.store');
        Route::put('payment-types/{paymentType}', [SettingsController::class, 'updatePaymentType'])->name('payment-types.update');
        Route::delete('payment-types/{paymentType}', [SettingsController::class, 'destroyPaymentType'])->name('payment-types.destroy');

        Route::get('finance-entry-types', [SettingsController::class, 'financeEntryTypes'])->name('finance-entry-types');
        Route::post('finance-entry-types', [SettingsController::class, 'storeFinanceEntryType'])->name('finance-entry-types.store');
        Route::put('finance-entry-types/{financeEntryType}', [SettingsController::class, 'updateFinanceEntryType'])->name('finance-entry-types.update');
        Route::delete('finance-entry-types/{financeEntryType}', [SettingsController::class, 'destroyFinanceEntryType'])->name('finance-entry-types.destroy');

        Route::get('terms', [SettingsController::class, 'terms'])->name('terms');
        Route::post('terms', [SettingsController::class, 'storeTerms'])->name('terms.store');
        Route::put('terms/{termsTemplate}', [SettingsController::class, 'updateTerms'])->name('terms.update');
        Route::delete('terms/{termsTemplate}', [SettingsController::class, 'destroyTerms'])->name('terms.destroy');

        Route::get('custom-fields', [SettingsController::class, 'customFields'])->name('custom-fields');
        Route::post('custom-fields', [SettingsController::class, 'storeCustomField'])->name('custom-fields.store');
        Route::put('custom-fields/{customField}', [SettingsController::class, 'updateCustomField'])->name('custom-fields.update');
        Route::delete('custom-fields/{customField}', [SettingsController::class, 'destroyCustomField'])->name('custom-fields.destroy');

        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    // Super-admin only: company & user management
    Route::middleware('super.admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('companies', CompanyController::class);
        Route::resource('roles', RoleController::class);
        Route::get('users', [UserController::class, 'adminIndex'])->name('users.index');
        Route::get('users/create', [UserController::class, 'adminCreate'])->name('users.create');
        Route::post('users', [UserController::class, 'adminStore'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'adminEdit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'adminUpdate'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'adminDestroy'])->name('users.destroy');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });
});
