<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'doLogin'])->name('doLogin');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});
