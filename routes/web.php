<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/data', [CustomerController::class, 'data'])->name('data');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::post('/{id}/change-status', [CustomerController::class, 'changeStatus'])->name('change-status');
        Route::post('/send-email', [CustomerController::class, 'sendEmail'])->name('send-email');
        Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
    });
});
