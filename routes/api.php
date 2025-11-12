<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuItemController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ===============================================
// ADMIN-ONLY ROUTES
// ===============================================
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {

    // /api/admin/menu-items (GET) - List all menu items
    Route::get('/menu-items', [MenuItemController::class, 'index']);

    // /api/admin/menu-items (POST) - Create a new menu item
    Route::post('/menu-items', [MenuItemController::class, 'store']);

});