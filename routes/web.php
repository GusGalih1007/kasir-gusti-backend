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
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

route::resource('category', CategoryController::class);
route::resource('brand', BrandController::class);
route::resource('supplier', SupplierController::class);
route::resource('customer', CustomerController::class);
route::resource('membership', MembershipController::class);
// route::resource('product', ProductController::class);
route::get('product', [ProductController::class, 'index'])->name('product.index');
route::get('product/create', [ProductController::class, 'create'])->name('product.create');
route::post('product', [ProductController::class, 'store'])->name('product.store');
route::get('product/{id}', [ProductController::class, 'show'])->name('product.show');
route::get('product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
route::put('product/{id}', [ProductController::class, 'update'])->name('product.update');
route::delete('product/{id}', [ProductController::class, 'destry'])->name('product.destroy');
// route::resource('{product}-variant', ProductVariantController::class);

route::get('product/{product}/variant', [ProductVariantController::class, 'index'])->name('product-variant.index');
route::get('product/{product}/variant/create', [ProductVariantController::class, 'create'])->name('product-variant.create');
route::post('product/{product}/variant', [ProductVariantController::class, 'store'])->name('product-variant.store');
route::get('product/{product}/variant/{id}', [ProductVariantController::class, 'show'])->name('product-variant.show');
route::get('product/{product}/variant/{id}/edit', [ProductVariantController::class, 'edit'])->name('product-variant.edit');
route::put('product/{product}/variant/{id}', [ProductVariantController::class, 'update'])->name('product-variant.update');
route::delete('product/{product}/variant/{id}', [ProductVariantController::class, 'destroy'])->name('product-variant.destroy');

route::resource('role-permission', RoleController::class);
route::resource('page', PageController::class);

route::get('login', [AuthController::class, 'loginPage'])->name('login.form');
route::post('login', [AuthController::class, 'loginWeb'])->name('login.post');
route::get('logout', [AuthController::class, 'logoutWeb'])->name('logout.user');

route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
