<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\api\DashboardController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Protected routes with permission middleware (it handles auth check internally)
Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('permission:read');


// AUTH
route::get('login', [AuthController::class, 'loginPage'])->name('login.form');
route::post('login', [AuthController::class, 'loginWeb'])->name('login.post');
route::get('logout', [AuthController::class, 'logoutWeb'])->name('logout.user');


// Profile Routes
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');



// CATEGORY
// route::resource('category', CategoryController::class)->middleware('permission:read');
route::get('category', [CategoryController::class, 'index'])->name('category.index')->middleware('permission:read');
route::get('category/create', [CategoryController::class, 'create'])->name('category.create')->middleware('permission:create');
route::post('category', [CategoryController::class, 'store'])->name('category.store')->middleware('permission:create');
route::get('category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit')->middleware('permission:update');
route::put('category/{id}', [CategoryController::class, 'update'])->name('category.update')->middleware('permission:update');
route::delete('category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy')->middleware('permission:delete');


// BRAND
// route::resource('brand', BrandController::class)->middleware('permission:read');
route::get('brand', [BrandController::class, 'index'])->name('brand.index')->middleware('permission:read');
route::get('brand/create', [BrandController::class, 'create'])->name('brand.create')->middleware('permission:create');
route::post('brand', [BrandController::class, 'store'])->name('brand.store')->middleware('permission:create');
route::get('brand/{id}/edit', [BrandController::class, 'edit'])->name('brand.edit')->middleware('permission:update');
route::put('brand/{id}', [BrandController::class, 'update'])->name('brand.update')->middleware('permission:update');
route::delete('brand/{id}', [BrandController::class, 'destroy'])->name('brand.destroy')->middleware('permission:delete');


// SUPPLIER
// route::resource('supplier', SupplierController::class);
route::get('supplier', [SupplierController::class, 'index'])->name('supplier.index')->middleware('permission:read');
route::get('supplier/create', [SupplierController::class, 'create'])->name('supplier.create')->middleware('permission:creat');
route::post('supplier', [SupplierController::class, 'store'])->name('supplier.store')->middleware('permission:create');
route::get('supplier/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit')->middleware('permission:update');
route::put('supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update')->middleware('permission:update');
route::delete('supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy')->middleware('permission:delete');


// CUSTOMER
// route::resource('customer', CustomerController::class);
route::get('customer', [CustomerController::class, 'index'])->name('customer.index')->middleware('permission:read');
route::get('customer/{id}', [CustomerController::class, 'show'])->name('customer.show')->middleware('permission:read');
route::get('customer/create', [CustomerController::class, 'create'])->name('customer.create')->middleware('permission:create');
route::post('customer', [CustomerController::class, 'store'])->name('customer.store')->middleware('permission:create');
route::get('customer/{id}', [CustomerController::class, 'show'])->name('customer.show')->middleware('permission:read');
route::get('customer/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit')->middleware('permission:update');
route::put('customer/{id}', [CustomerController::class, 'update'])->name('customer.update')->middleware('permission:update');
route::delete('customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy')->middleware('permission:delete');


// MEMBERSHIP
// route::resource('membership', MembershipController::class);
route::get('membership', [MembershipController::class, 'index'])->name('membership.index')->middleware('permission:read');
route::get('membership/create', [MembershipController::class, 'create'])->name('membership.create')->middleware('permission:create');
route::post('membership', [MembershipController::class, 'store'])->name('membership.store')->middleware('permission:create');
route::get('membership/{id}/edit', [MembershipController::class, 'edit'])->name('membership.edit')->middleware('permission:update');
route::put('membership/{id}', [MembershipController::class, 'update'])->name('membership.update')->middleware('permission:udpate');
route::delete('membership/{id}', [MembershipController::class, 'destroy'])->name('membership.destroy')->middleware('permission:delete');


// ROLE PERMISSION
route::get('role-permission', [RoleController::class, 'index'])->name('role-permission.index')->middleware('permission:read');
route::post('role-permission', [RoleController::class, 'store'])->name('role-permission.store')->middleware('permission:create');
route::put('role-permission/{id}', [RoleController::class, 'update'])->name('role-permission.update')->middleware('permission:update');
route::delete('role-permission/{id}', [RoleController::class, 'destroy'])->name('role-permission.destroy')->middleware('permission:delete');
route::post('/role-permission/update-permission', [RoleController::class, 'updatePermission'])->name('role-permission.update-permission')->middleware('permission:create');


// USER
// route::resource('user', UserController::class);
route::get('user', [UserController::class, 'index'])->name('user.index')->middleware('permission:read');
route::get('user/{id}', [UserController::class, 'show'])->name('user.show')->middleware('permission:read');
Route::patch('/user/{id}/activate', [UserController::class, 'activate'])->name('user.activate')->middleware('permission:update');
Route::patch('/user/{id}/deactivate', [UserController::class, 'deactivate'])->name('user.deactivate')->middleware('permission:update');
route::get('user/create', [UserController::class, 'create'])->name('user.create')->middleware('permission:create');
route::post('user', [UserController::class, 'store'])->name('user.store')->middleware('permission:create');
route::get('user/{id}/edit', [UserController::class, 'edit'])->name('user.edit')->middleware('permission:update');
route::put('user/{id}', [UserController::class, 'update'])->name('user.update')->middleware('permission:update');
route::delete('user/{id}', [UserController::class, 'destroy'])->name('user.destroy')->middleware('permission:delete');


// PRODUCT
route::get('product', [ProductController::class, 'index'])->name('product.index')->middleware('permission:read');
route::get('product/create', [ProductController::class, 'create'])->name('product.create')->middleware('permission:create');
route::post('product', [ProductController::class, 'store'])->name('product.store')->middleware('permission:create');
route::get('product/{id}', [ProductController::class, 'show'])->name('product.show')->middleware('permission:read');
route::get('product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit')->middleware('permission:update');
route::put('product/{id}', [ProductController::class, 'update'])->name('product.update')->middleware('permission:update');
route::delete('product/{id}', [ProductController::class, 'destroy'])->name('product.destroy')->middleware('permission:delete');


// PRODUCT VARIANT
route::get('product/{product}/variant', [ProductVariantController::class, 'index'])->name('product-variant.index')->middleware('permission:read');
route::get('product/{product}/variant/create', [ProductVariantController::class, 'create'])->name('product-variant.create')->middleware('permission:create');
route::post('product/{product}/variant', [ProductVariantController::class, 'store'])->name('product-variant.store')->middleware('permission:create');
route::get('product/{product}/variant/{id}', [ProductVariantController::class, 'show'])->name('product-variant.show')->middleware('permission:read');
route::get('product/{product}/variant/{id}/edit', [ProductVariantController::class, 'edit'])->name('product-variant.edit')->middleware('permission:update');
route::put('product/{product}/variant/{id}', [ProductVariantController::class, 'update'])->name('product-variant.update')->middleware('permission:update');
route::delete('product/{product}/variant/{id}', [ProductVariantController::class, 'destroy'])->name('product-variant.destroy')->middleware('permission:delete');


// TRANSACTION
route::get('transaction', [TransactionController::class, 'index'])->name('transaction.index')->middleware('permission:read');
route::get('transaction/create', [TransactionController::class, 'create'])->name('transaction.create')->middleware('permission:create');
route::post('transaction', [TransactionController::class, 'store'])->name('transaction.store')->middleware('permission:create');
Route::get('transaction/{id}', [TransactionController::class, 'show'])->name('transaction.show')->middleware('permission:read');

// Midtrans payment routes
Route::match(['GET', 'POST'], '/transaction/payment/callback', [TransactionController::class, 'paymentCallback'])->name('transaction.payment.callback');
Route::get('/transaction/{order}/payment', [TransactionController::class, 'showPayment'])->name('transaction.payment');
Route::get('/transaction/{order}/payment/finish', [TransactionController::class, 'paymentFinish'])->name('transaction.payment.finish');
Route::get('/api/transaction/{order}/check-status', [TransactionController::class, 'checkPaymentStatus'])->name('transaction.check-status');
// Route::get('transaction/{order}/check-status', [TransactionController::class, 'checkPaymentStatus'])->name('transaction.check-status');

// Export routes
Route::get('export/transactions/pdf', [ExportController::class, 'exportTransactionsPdf'])->name('export.transactions.pdf')->middleware('permission:create');
Route::get('export/transactions/excel', [ExportController::class, 'exportTransactionsExcel'])->name('export.transactions.excel')->middleware('permission:create');
Route::get('export/transactions/print', [ExportController::class, 'exportTransactionsPrint'])->name('export.transactions.print')->middleware('permission:create');
Route::get('export/receipt/{orderId}/pdf', [ExportController::class, 'exportReceiptPdf'])->name('export.receipt.pdf');
Route::get('export/sales-report', [ExportController::class, 'exportSalesReport'])->name('export.sales-report.pdf');
