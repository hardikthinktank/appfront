<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;


 //Public Routes
Route::get('/', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Admin Authentication
Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

//Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Products Management (CRUD)
    Route::resource('products', AdminController::class)->except(['show']);
});
