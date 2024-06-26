<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// Sanitize all routes
Route::prefix('admin')->group(function () {
    // Define the 'api/v1/admin' prefixed routes here
    Route::post('/login', [UserController::class, 'login'])->name('userAdmin.login');

    // CRUD
    Route::middleware('auth:api')->group(function () {
        Route::match(['get', 'head'], '/logout', [UserController::class, 'logout'])->name('userAdmin.logout');

        Route::middleware('can:manage-admin-accounts')->group(function () {
            Route::post('/create', [UserController::class, 'create'])->name('userAdmin.create');
            Route::match(['get', 'head'], '/user-listing', [UserController::class, 'index'])->name('userAdmin.userListing');

            Route::put('/user-edit/{user}', [UserController::class, 'update'])->name('userAdmin.userEdit');
            Route::delete('/user-delete/{user}', [UserController::class, 'destroy'])->name('userAdmin.userDelete');
        });
    });
});

Route::prefix('user')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::match(['get', 'head'], '/', [UserController::class, 'show'])->name('user.show');
        Route::delete('/', [UserController::class, 'destroy'])->name('user.destroy');
        Route::get('/orders', [UserController::class, 'listOrders'])->name('user.orders');
        Route::get('/logout', [UserController::class, 'logout'])->name('user.logout');
        Route::put('/edit', [UserController::class, 'update'])->name('user.edit');
    });

    Route::post('/login', [UserController::class, 'login'])->name('user.login');
    Route::post('/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->name('user.forgotPassword');
    Route::post('/reset-password-token/{token}', [UserController::class, 'resetPassword'])->name('user.resetPassword');
});
