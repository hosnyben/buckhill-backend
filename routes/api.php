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
            Route::missing(function (Request $request) {
                return response()->apiError(new Exception('User not found'), Response::HTTP_NOT_FOUND);
            })->group(function () {
                Route::put('/user-edit/{user}', [UserController::class, 'update'])->name('userAdmin.userEdit');
                Route::delete('/user-delete/{user}', [UserController::class, 'destroy'])->name('userAdmin.userDelete');
            });
        });
    });
});
