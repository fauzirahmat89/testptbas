<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('customers.index');
});

Route::prefix('customers')->name('customers.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/data', [CustomerController::class, 'data'])->name('data');
    Route::post('/', [CustomerController::class, 'store'])->name('store');
});
