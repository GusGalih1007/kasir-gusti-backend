<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\api\DashboardController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
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
route::resource('product', ProductController::class);
route::get('product/{id}/variant', [ProductVariantController::class, 'index'])->name('product-variant.index');
route::get('product/{id}/variant/{variant}', [ProductVariantController::class, 'show'])->name('product-variant.show');
route::get('product/{id}/variant/create', [ProductVariantController::class, 'create'])->name('product-variant.create');
route::post('product/{id}/variant', [ProductVariantController::class, 'store'])->name('product-variant.store');
route::get('product/{id}/variant/{variant}/edit', [ProductVariantController::class, 'edit'])->name('product-variant.edit');
route::put('product/{id}/variant/{variant}', [ProductVariantController::class, 'update'])->name('product-variant.update');
route::delete('product/{id}/variant/{variant}', [ProductVariantController::class, 'destroy'])->name('product-variant.destroy');

route::get('login', [AuthController::class, 'loginPage'])->name('login.form');
route::post('login', [AuthController::class, 'loginWeb'])->name('login.post');
route::get('logout', [AuthController::class, 'logoutWeb'])->name('logout.user');

route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
