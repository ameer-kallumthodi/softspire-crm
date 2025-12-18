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

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Leads - AJAX routes must come before resource routes
    Route::get('/leads/ajax/add', [LeadController::class, 'ajaxAdd'])->name('leads.ajax-add');
    Route::get('/leads/{lead}/ajax/edit', [LeadController::class, 'ajaxEdit'])->name('leads.ajax-edit');
    Route::get('/leads/{lead}/ajax/convert', [LeadController::class, 'ajaxConvert'])->name('leads.ajax-convert');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::post('/leads/{lead}/status-update', [LeadController::class, 'updateStatus'])->name('leads.status-update');
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
    
    // Users - AJAX routes must come before resource routes
    Route::get('/users/ajax/add', [UsersController::class, 'ajaxAdd'])->name('users.ajax-add');
    Route::get('/users/{user}/ajax/edit', [UsersController::class, 'ajaxEdit'])->name('users.ajax-edit');
    Route::get('/users/{id}', [UsersController::class, 'show'])->name('users.show');
    Route::resource('users', UsersController::class);
    
    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    
    // Quotations
    Route::get('/customers/{customer}/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/customers/{customer}/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/customers/{customer}/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

