<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockMovementController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('users', UserController::class)->except(['show','edit','update']);
});

Route::middleware('auth')->group(function () {

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Item routes
    Route::resource('items', ItemController::class);

    // Additional routes for items
    Route::get('items/{item}/stock-movements/create/{type}', [StockMovementController::class, 'create'])
        ->whereIn('type', ['in', 'out', 'adjustment'])
        ->name('stock-movements.create');

    Route::post('items/{item}/stock-movements', [StockMovementController::class, 'store'])
        ->name('stock-movements.store');
});

require __DIR__ . '/auth.php';
