<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('');
});

route::resource('category', CategoryController::class);

route::get('login', [AuthController::class, 'loginPage'])->name('login.form');
route::post('login', [AuthController::class, 'loginWeb'])->name('login.post');
route::get('logout', [AuthController::class, 'logoutWeb'])->name('logout.user');

route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
