<?php

namespace App\Http\Controllers\api;

use App\Helpers\GetUserRoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customers;
use App\Models\Membership;
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
        $membershipData = Membership::with('customer')->get();
        $transactionData = Order::get();
        $productVariantData = ProductVariant::get();
        $supplierData = Supplier::get();
        $brandData = Brand::get();
        $categoryData = Category::get();
        $productData = Product::get();
        $userData = Users::get();
        $user = auth()->guard('web')->user();

        $getRole = GetUserRoleHelper::getRoleName();

        // dd($membershipData);

        return view('dashboard.index', compact(
            'user',
            'userData',
            'customerData',
            'membershipData',
            'transactionData',
            'productData',
            'productVariantData',
            'supplierData',
            'brandData',
            'categoryData',
            'getRole'
        ));
    }
}
