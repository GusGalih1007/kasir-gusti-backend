<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PageRoleActionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//auth
route::post('login', [AuthController::class, 'login']);
route::post('logout', [AuthController::class, 'logout']);

//category
route::apiResource('category', CategoryController::class);

//customer
route::apiResource('customer', CustomerController::class);

//role
route::apiResource('role', RoleController::class);

//user
route::apiResource('user', UserController::class);

//brand
route::apiResource('brand', BrandController::class);

//supplier
route::apiResource('supplier', SupplierController::class);

//product
route::apiResource('product', ProductController::class);

//product-variant
route::apiResource('product-variant', ProductVariantController::class);

//page action
route::get('page-role', [PageController::class, 'index'])->name('pageRole.getAll');
route::get('page-role/{page-code}', [PageController::class, 'show'])->name('pageRole.getByCode');
route::get('page-role-action', [PageRoleActionController::class, 'index'])->name('pageRoleAction.getAll');
route::get('page-role-action/{role}', [PageRoleActionController::class, 'getByRole'])->name('pageRoleAction.getByRole');
route::get('page-role-action/{page-code}', [PageRoleActionController::class, 'getByPage'])->name('pageRoleAction.getByPage');