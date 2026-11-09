<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes - AzureMerch POS & Inventory Management System
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'kasir' => redirect()->route('pos.index'),
            'gudang' => redirect()->route('warehouse.index'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Profile Management Routes (Accessible by all logged in roles)
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
});

/*
|--------------------------------------------------------------------------
| Module A: Kasir (Cashier) - Desktop UI Focus
| Middleware: UKasir (Strict Role Based Access Control)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'UKasir'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::get('/search', [PosController::class, 'search'])->name('search');
    Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
    Route::get('/receipt/{id}', [PosController::class, 'receipt'])->name('receipt');
    Route::get('/history', [PosController::class, 'history'])->name('history');
});

/*
|--------------------------------------------------------------------------
| Module B: Gudang (Warehouse Staff) - Mobile-First Responsive
| Middleware: UGudang (Strict Role Based Access Control)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'UGudang'])->prefix('warehouse')->name('warehouse.')->group(function () {
    Route::get('/', [WarehouseController::class, 'index'])->name('index');
    Route::get('/products/create', [WarehouseController::class, 'create'])->name('create');
    Route::post('/products', [WarehouseController::class, 'store'])->name('store');
    Route::get('/products/{product}/edit', [WarehouseController::class, 'edit'])->name('edit');
    Route::put('/products/{product}', [WarehouseController::class, 'update'])->name('update');
    Route::delete('/products/{product}', [WarehouseController::class, 'destroy'])->name('destroy');
    
    // Inbound Shipment Management
    Route::get('/inbound', [WarehouseController::class, 'inbound'])->name('inbound');
    Route::post('/inbound', [WarehouseController::class, 'processInbound'])->name('process-inbound');
    
    // Stock Opname Adjustments & Logs
    Route::post('/adjustment', [WarehouseController::class, 'adjustment'])->name('adjustment');
    Route::get('/logs', [WarehouseController::class, 'logs'])->name('logs');
});

/*
|--------------------------------------------------------------------------
| Module C: Admin - Executive Reports & Financial Analytics
| Middleware: UAdmin (Strict Role Based Access Control)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'UAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit-logs');
});
