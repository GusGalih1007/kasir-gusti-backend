<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\api\DashboardController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\MembershipController;
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

route::get('login', [AuthController::class, 'loginPage'])->name('login.form');
route::post('login', [AuthController::class, 'loginWeb'])->name('login.post');
route::get('logout', [AuthController::class, 'logoutWeb'])->name('logout.user');

route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
