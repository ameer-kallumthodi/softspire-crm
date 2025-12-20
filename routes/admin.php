<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\PurposeController;
use App\Http\Controllers\Admin\LeadStatusController;
use App\Http\Controllers\Admin\LeadSourceController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\TelecallerController;
use App\Http\Controllers\Admin\ManagerController;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Redirect /admin/ to /admin/dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Leads - AJAX routes must come before resource routes
    Route::get('/leads/ajax/add', [LeadController::class, 'ajaxAdd'])->name('leads.ajax-add');
    Route::get('/leads/{lead}/ajax/edit', [LeadController::class, 'ajaxEdit'])->name('leads.ajax-edit');
    Route::get('/leads/{lead}/ajax/convert', [LeadController::class, 'ajaxConvert'])->name('leads.ajax-convert');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::post('/leads/{lead}/status-update', [LeadController::class, 'updateStatus'])->name('leads.status-update');
    Route::get('/leads/bulk-upload', [LeadController::class, 'bulkUploadView'])->name('leads.bulk-upload');
    Route::get('/leads/bulk-upload/template', [LeadController::class, 'downloadTemplate'])->name('leads.bulk-upload.template');
    Route::post('/leads/bulk-upload', [LeadController::class, 'bulkUploadSubmit'])->name('leads.bulk-upload.submit');
    Route::get('/leads/bulk-reassign', [LeadController::class, 'ajaxBulkReassign'])->name('leads.bulk-reassign');
    Route::post('/leads/bulk-reassign', [LeadController::class, 'bulkReassign'])->name('leads.bulk-reassign.submit');
    Route::post('/leads/get-by-source-reassign', [LeadController::class, 'getLeadsBySourceReassign'])->name('leads.get-by-source-reassign');
    Route::get('/leads/telecallers', [LeadController::class, 'getTelecallersByTeam'])->name('leads.telecallers');
    Route::resource('leads', LeadController::class);
    
    // Countries - AJAX routes must come before resource routes
    Route::get('/countries/ajax/add', [CountryController::class, 'ajaxAdd'])->name('countries.ajax-add');
    Route::get('/countries/{country}/ajax/edit', [CountryController::class, 'ajaxEdit'])->name('countries.ajax-edit');
    Route::post('/countries/{country}/toggle-status', [CountryController::class, 'toggleStatus'])->name('countries.toggle-status');
    Route::resource('countries', CountryController::class);
    
    // Purposes - AJAX routes must come before resource routes
    Route::get('/purposes/ajax/add', [PurposeController::class, 'ajaxAdd'])->name('purposes.ajax-add');
    Route::get('/purposes/{purpose}/ajax/edit', [PurposeController::class, 'ajaxEdit'])->name('purposes.ajax-edit');
    Route::post('/purposes/{purpose}/toggle-status', [PurposeController::class, 'toggleStatus'])->name('purposes.toggle-status');
    Route::resource('purposes', PurposeController::class);
    
    // Lead Statuses - AJAX routes must come before resource routes
    Route::get('/lead-statuses/ajax/add', [LeadStatusController::class, 'ajaxAdd'])->name('lead-statuses.ajax-add');
    Route::get('/lead-statuses/{leadStatus}/ajax/edit', [LeadStatusController::class, 'ajaxEdit'])->name('lead-statuses.ajax-edit');
    Route::post('/lead-statuses/{leadStatus}/toggle-status', [LeadStatusController::class, 'toggleStatus'])->name('lead-statuses.toggle-status');
    Route::resource('lead-statuses', LeadStatusController::class);
    
    // Lead Sources - AJAX routes must come before resource routes
    Route::get('/lead-sources/ajax/add', [LeadSourceController::class, 'ajaxAdd'])->name('lead-sources.ajax-add');
    Route::get('/lead-sources/{leadSource}/ajax/edit', [LeadSourceController::class, 'ajaxEdit'])->name('lead-sources.ajax-edit');
    Route::post('/lead-sources/{leadSource}/toggle-status', [LeadSourceController::class, 'toggleStatus'])->name('lead-sources.toggle-status');
    Route::resource('lead-sources', LeadSourceController::class);
    
    // Users routes removed - using separate Telecallers and Managers controllers instead
    
    // Telecallers - AJAX routes must come before resource routes
    Route::get('/telecallers/ajax/add', [TelecallerController::class, 'ajaxAdd'])->name('telecallers.ajax-add');
    Route::get('/telecallers/{id}/ajax/edit', [TelecallerController::class, 'ajaxEdit'])->name('telecallers.ajax-edit');
    Route::get('/telecallers/{id}/reset-password', [TelecallerController::class, 'resetPassword'])->name('telecallers.reset-password');
    Route::put('/telecallers/{id}/reset-password', [TelecallerController::class, 'updatePassword'])->name('telecallers.update-password');
    Route::get('/telecallers/{id}', [TelecallerController::class, 'show'])->name('telecallers.show');
    Route::resource('telecallers', TelecallerController::class)->parameters(['telecallers' => 'id']);
    
    // Managers - AJAX routes must come before resource routes
    Route::get('/managers/ajax/add', [ManagerController::class, 'ajaxAdd'])->name('managers.ajax-add');
    Route::get('/managers/{id}/ajax/edit', [ManagerController::class, 'ajaxEdit'])->name('managers.ajax-edit');
    Route::get('/managers/{id}/reset-password', [ManagerController::class, 'resetPassword'])->name('managers.reset-password');
    Route::put('/managers/{id}/reset-password', [ManagerController::class, 'updatePassword'])->name('managers.update-password');
    Route::get('/managers/{id}', [ManagerController::class, 'show'])->name('managers.show');
    Route::resource('managers', ManagerController::class)->parameters(['managers' => 'id']);
    
    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    
    // Quotations
    Route::get('/customers/{customer}/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/customers/{customer}/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/customers/{customer}/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');
    Route::put('/quotations/{quotation}/accept', [QuotationController::class, 'accept'])->name('quotations.accept');
    
    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/pending-quotations', [PaymentController::class, 'pendingQuotations'])->name('payments.pending-quotations');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'generateReceiptPDF'])->name('payments.receipt');
    Route::get('/payments/pending-amount', [PaymentController::class, 'getPendingAmount'])->name('payments.pending-amount');
    Route::get('/payments/check-transaction-id', [PaymentController::class, 'checkTransactionId'])->name('payments.check-transaction-id');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

