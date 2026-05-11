<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\TransactionsController;
use Illuminate\Support\Facades\Route;

// API v1 routes with prefix
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::get('/products', [ProductsController::class, 'index']);
        Route::get('/products/{product}', [ProductsController::class, 'show']);
        Route::post('/products/{product}/purchase', [ProductsController::class, 'purchase']);

        Route::middleware('admin:api')->group(function () {
            Route::post('/products', [ProductsController::class, 'store']);
            Route::put('/products/{product}', [ProductsController::class, 'update']);
            Route::delete('/products/{product}', [ProductsController::class, 'destroy']);
            Route::get('/statistics/products', [ProductsController::class, 'statistics']);

            Route::get('/transactions', [TransactionsController::class, 'index']);
            Route::get('/transactions/{transaction}', [TransactionsController::class, 'show']);
            Route::get('/statistics/transactions', [TransactionsController::class, 'statistics']);
        });
    });
});

if (app()->environment('local', 'testing')) {
    Route::get('/api-documentation', function () {
        return view('swagger-ui::index');
    })->name('api.documentation');

    Route::get('/api/docs', function () {
        $docsPath = public_path('api/docs.json');
        if (file_exists($docsPath)) {
            return response()->file($docsPath, [
                'Content-Type' => 'application/json'
            ]);
        }
        return response()->json([
            'error' => 'API documentation not found'
        ], 404);
    })->name('api.docs');

    Route::get('/api/docs.json', function () {
        $docsPath = public_path('api/docs.json');
        if (file_exists($docsPath)) {
            return response()->file($docsPath, [
                'Content-Type' => 'application/json'
            ]);
        }
        return response()->json([
            'error' => 'API documentation not found'
        ], 404);
    })->name('api.docs.json');
}

Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'API endpoint not found'
    ], 404);
});
