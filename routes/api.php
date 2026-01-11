<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\WeeklyMenuController;

// ===============================================
// API ROUTES
// ===============================================

// Public routes (no login required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (login required)
// 'auth:sanctum' is the middleware that protects these routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});


// ===============================================
// ADMIN-ONLY ROUTES
// ===============================================
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {

    // /api/admin/menu-items (GET) - List all menu items
    Route::get('/menu-items', [MenuItemController::class, 'index']);

    // /api/admin/menu-items (POST) - Create a new menu item
    Route::post('/menu-items', [MenuItemController::class, 'store']);

    // /api/admin/weekly-menus (POST) - Create a new weekly menu
    Route::post('/weekly-menus', [WeeklyMenuController::class, 'store']);

    // /api/admin/weekly-menus (GET) - List all weekly menus
    Route::get('/weekly-menus', [WeeklyMenuController::class, 'index']);

    // /api/admin/weekly-menus/{id} (GET) - Get one weekly menu (by ID)
    Route::get('/weekly-menus/{weeklyMenu}', [WeeklyMenuController::class, 'show']);
});