<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PosApiController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductApiController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CustomerController;

Route::get('/', fn() => view('index'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/stats', [DashboardApiController::class, 'stats']);
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/api/reports/stats', [ReportsController::class, 'stats']);
    Route::get('/api/reports/export-excel', [ReportsController::class, 'exportExcel']);

    // Products API
    Route::prefix('api/products')->group(function () {
        Route::get('/', [ProductApiController::class, 'index']);
        Route::post('/save', [ProductApiController::class, 'save']);
        Route::post('/delete', [ProductApiController::class, 'delete']);
    });

    // POS API
    Route::prefix('api/pos')->group(function () {
        Route::get('/products', [ProductApiController::class, 'posProducts']);
        Route::post('/save-order', [PosApiController::class, 'saveOrder']);
    });

    // Expenses API
    Route::prefix('api/expenses')->group(function () {
        Route::get('/', [ExpenseController::class, 'list']);
        Route::post('/save', [ExpenseController::class, 'save']);
        Route::post('/delete', [ExpenseController::class, 'delete']);
    });

    // Customers API
    Route::prefix('api/customers')->group(function () {
        Route::get('/', [CustomerController::class, 'list']);
        Route::get('/lookup', [CustomerController::class, 'lookup']);
        Route::post('/save', [CustomerController::class, 'save']);
        Route::post('/delete', [CustomerController::class, 'delete']);
        Route::post('/add-points', [CustomerController::class, 'addPoints']);
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
