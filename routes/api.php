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
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('customers', [TransactionController::class, 'getCustomers']);
Route::get('categories/{categoryId}/subcategories', [TransactionController::class, 'getSubCategories']);
Route::get('categories/{categoryId}/products', [TransactionController::class, 'getProductsByCategory']);
Route::get('subcategories/{subCategoryId}/products', [TransactionController::class, 'getProductsBySubCategory']);
Route::get('products/{productId}/variants', [TransactionController::class, 'getVariantsByProduct']);

// Midtrans webhook
Route::post('midtrans/notification', [TransactionController::class, 'midtransNotification']);
// Check payment status
// Route::get('transaction/{order}/check-status', [TransactionController::class, 'checkPaymentStatus'])->name('transaction.check-status');


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//auth
// Route::post('login', [AuthController::class, 'login']);
// Route::post('logout', [AuthController::class, 'logout']);
// Route::get('me', [AuthController::class, 'me']);

// route::middleware('jwt')->group(function () {
//     //category
//     route::apiResource('category', CategoryController::class);

//     //customer
//     route::apiResource('customer', CustomerController::class);

//     //role
//     route::apiResource('role', RoleController::class);

//     //user
//     route::apiResource('user', UserController::class);

//     //brand
//     route::apiResource('brand', BrandController::class);

//     //supplier
//     route::apiResource('supplier', SupplierController::class);

//     //product
//     route::apiResource('product', ProductController::class);

//     //product-variant
//     route::apiResource('product-variant', ProductVariantController::class);

//     //page action
//     route::get('page-action', [PageController::class, 'index'])->name('pageRole.getAll');
//     route::get('page-action/{page-code}', [PageController::class, 'show'])->name('pageRole.getByCode');
//     route::get('page-role-action', [PageRoleActionController::class, 'index'])->name('pageRoleAction.getAll');
//     route::get('page-role-action/role/{role}', [PageRoleActionController::class, 'getByRole'])->name('pageRoleAction.getByRole');
//     route::get('page-role-action/page/{page-code}', [PageRoleActionController::class, 'getByPage'])->name('pageRoleAction.getByPage');

//     //transactions\
//     route::get('transaction', [TransactionController::class, 'index']);
//     route::get('transaction/{id}', [TransactionController::class, 'show']);
//     route::post('transaction', [TransactionController::class, 'store']);
//     route::delete('transaction/{id}', [TransactionController::class, 'destroy']);
// });
