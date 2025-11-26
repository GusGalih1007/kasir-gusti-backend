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
use App\Models\Supplier;
use App\Models\Users;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $customerData = Customers::orderBy('created_at', 'desc')->get();
        $membershipData = Membership::orderBy('created_at', 'desc')->with('customer')->get();
        $transactionData = Order::orderBy('created_at', 'desc')->get();
        $productVariantData = ProductVariant::orderBy('created_at', 'desc')->get();
        $supplierData = Supplier::orderBy('created_at', 'desc')->get();
        $brandData = Brand::orderBy('created_at', 'desc')->get();
        $categoryData = Category::orderBy('created_at', 'desc')->get();
        $productData = Product::orderBy('created_at', 'desc')->get();
        $userData = Users::orderBy('created_at', 'desc')->get();
        $user = auth()->guard('web')->user();

        $getRole = GetUserRoleHelper::getRoleName();

        // Data untuk chart - revenue 6 bulan terakhir
        $monthlyRevenue = Order::selectRaw('
            YEAR(created_at) as year,
            MONTH(created_at) as month,
            SUM(total_amount) as total_revenue,
            COUNT(*) as order_count
        ')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Format data untuk chart - pastikan selalu 6 bulan
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        // Generate 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $year = $date->year;
            $month = $date->month;

            $chartLabels[] = $monthName;

            // Cari data yang sesuai dari query
            $monthData = $monthlyRevenue->first(function ($item) use ($year, $month) {
                return $item->year == $year && $item->month == $month;
            });

            if ($monthData) {
                $chartRevenue[] = (float) $monthData->total_revenue;
                $chartOrders[] = (int) $monthData->order_count;
            } else {
                $chartRevenue[] = 0;
                $chartOrders[] = 0;
            }
        }

        return view('dashboard.deepseek', compact(
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
            'getRole',
            'chartLabels',
            'chartRevenue',
            'chartOrders'
        ));
    }
}
