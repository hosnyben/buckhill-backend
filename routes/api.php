<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

            // Handle 404 route model binding
            Route::put('/user-edit/{user}', [UserController::class, 'update'])->name('userAdmin.userEdit')->missing(function (Request $request) {
                return response()->apiError(new Exception('User not found'), Response::HTTP_NOT_FOUND);
            });
            Route::delete('/user-delete/{user}', [UserController::class, 'destroy'])->name('userAdmin.userDelete');
        });
    });
});

Route::prefix('user')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::match(['get', 'head'], '/', [UserController::class, 'show'])->name('user.show');
        Route::delete('/', [UserController::class, 'destroy'])->name('user.destroy');
        Route::get('/orders', [UserController::class, 'listOrders'])->name('user.orders');
        
        Route::put('/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::get('/logout', [UserController::class, 'logout'])->name('user.logout');
    });

    Route::post('/login', [UserController::class, 'login'])->name('user.login');
    Route::post('/create', [UserController::class, 'login'])->name('user.create');
    Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->name('user.forgotPassword');
    Route::post('/reset-password-token', [UserController::class, 'resetPassword'])->name('user.resetPassword');
});