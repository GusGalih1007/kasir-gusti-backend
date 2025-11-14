<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customers;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Users;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $customerData = Customers::get();
        $transactionData = Order::get();
        $productVariantData = ProductVariant::get();
        $supplierData = Supplier::get();
        $brandData = Brand::get();
        $categoryData = Category::get();
        $productData = Product::get();
        $userData = Users::get();
        $user = auth()->guard('web')->user();

        // dd($user->role_id);

        switch ($user->role_id)
        {
            case $user->role_id = 1:
                return view('dashboard.admin');
            case $user->role_id = 2:
                return view('dashboard.cashier');
            case $user->role_id = 3:
                return view('dashboard.warehouse');
        }
    }
}
