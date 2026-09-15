<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {

    // Category routes (Admin only)
    Route::resource('categories', CategoryController::class);

    // User routes (Admin only)
    Route::resource('users', UserController::class)->except(['show', 'edit', 'update']);
    Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
});

Route::middleware('auth')->group(function () {

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Item routes
    Route::resource('items', ItemController::class);

    // Additional routes for items
    Route::get('items/{item}/stock-movements/create/{type}', [StockMovementController::class, 'create'])
        ->whereIn('type', ['in', 'out', 'adjustment'])
        ->name('stock-movements.create');
    // Stock Movement routes
    Route::post('items/{item}/stock-movements', [StockMovementController::class, 'store'])
        ->name('stock-movements.store');

    // Inventory Transaction routes
    Route::get('inventory-transactions', [InventoryTransactionController::class, 'index'])
        ->name('inventory-transactions.index');
});

require __DIR__.'/auth.php';
