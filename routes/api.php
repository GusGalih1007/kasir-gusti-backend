<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PageRoleActionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

route::apiResource('categories', CategoryController::class);
route::apiResource('customer', CustomerController::class);
route::apiResource('role', RoleController::class);
route::apiResource('user', UserController::class);
route::apiResource('brand', BrandController::class);
route::get('page-role', [PageController::class, 'index'])->name('pageRole.getAll');
route::get('page-role/{page-code}', [PageController::class, 'show'])->name('pageRole.getByCode');
route::get('page-role-action', [PageRoleActionController::class, 'index'])->name('pageRoleAction.getAll');
route::get('page-role-action/{role}', [PageRoleActionController::class, 'getByRole'])->name('pageRoleAction.getByRole');
route::get('page-role-action/{page-code}', [PageRoleActionController::class, 'getByPage'])->name('pageRoleAction.getByPage');