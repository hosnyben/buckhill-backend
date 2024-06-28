<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\BrandController;

Route::prefix('admin')->group(function () {
    Route::post('/login', [UserController::class, 'login'])->name('userAdmin.login');

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
        Route::put('/edit', [UserController::class, 'update'])->name('user.update');
    });

    Route::post('/login', [UserController::class, 'login'])->name('user.login');
    Route::post('/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->name('user.forgotPassword');
    Route::post('/reset-password-token/{token}', [UserController::class, 'resetPassword'])->name('user.resetPassword');
});

Route::prefix('main')->group(function () {
    Route::prefix('blog')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('blog.index');
        Route::get('/{post}', [PostController::class, 'show'])->name('blog.show');
    });

    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
});

Route::prefix('brand')->group(function () {
    Route::match(['get', 'head'], '/{brand}', [BrandController::class, 'show'])->name('brand.show');

    Route::middleware(['auth:api','can:manage-admin-accounts'])->group(function () {
        Route::post('/create', [BrandController::class, 'create'])->name('brand.create');
        Route::match(['put', 'patch'], '/{brand}', [BrandController::class, 'update'])->name('brand.update');
        Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('brand.delete');
    });
});
Route::match(['get', 'head'], '/brands', [BrandController::class, 'index'])->name('brand.index');
