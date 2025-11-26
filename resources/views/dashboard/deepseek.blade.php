@extends('layout.app')
@section('title', "{$getRole} Dashboard")
@section('content')
    <div class="row">
        <!-- Quick Stats Row 1 -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="bg-info text-white rounded p-3">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" width="20px" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="text-end">
                            Customers
                            <h2 class="counter">{{ count($customerData) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="bg-warning text-white rounded p-3">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" width="20px" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="text-end">
                            Products
                            <h2 class="counter">{{ count($productData) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="bg-success text-white rounded p-3">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" width="20px" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="text-end">
                            Orders
                            <h2 class="counter">{{ count($transactionData) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="bg-danger text-white rounded p-3">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" width="20px" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="text-end">
                            Revenue (Rp)
                            <h2 class="counter">{{ number_format($transactionData->sum('total_amount') / 1000, 1) }}K</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Statistics -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">Product Availability</div>
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <div>
                            <h2 class="counter">{{ count($productData) }}</h2>
                            {{ number_format((count($productData) / 100) * 100, 1) }}%
                        </div>
                        <div class="border bg-success-subtle rounded p-3">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" width="20px" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="progress bg-success-subtle shadow-none w-100" style="height: 6px">
                            <div class="progress-bar bg-success" data-toggle="progress-bar" role="progressbar"
                                aria-valuenow="{{ min(100, (count($productData) / 50) * 100) }}" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">Order Completion</div>
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <div>
                            <h2 class="counter">{{ $transactionData->where('status', 'completed')->count() }}</h2>
                            {{ $transactionData->count() > 0 ? number_format(($transactionData->where('status', 'completed')->count() / $transactionData->count()) * 100, 1) : 0 }}%
                        </div>
                        <div class="border bg-info-subtle rounded p-3">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" width="20px" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="progress bg-info-subtle shadow-none w-100" style="height: 6px">
                            <div class="progress-bar bg-info" data-toggle="progress-bar" role="progressbar"
                                aria-valuenow="{{ $transactionData->count() > 0 ? ($transactionData->where('status', 'completed')->count() / $transactionData->count()) * 100 : 0 }}"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">Customer Growth</div>
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <div>
                            <h2 class="counter">{{ count($customerData) }}</h2>
                            {{ number_format((count($customerData) / 200) * 100, 1) }}%
                        </div>
                        <div class="border bg-warning-subtle rounded p-3">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" width="20px" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="progress bg-warning-subtle shadow-none w-100" style="height: 6px">
                            <div class="progress-bar bg-warning" data-toggle="progress-bar" role="progressbar"
                                aria-valuenow="{{ min(100, (count($customerData) / 200) * 100) }}" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">Revenue Target (Rp)</div>
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <div>
                            <h2 class="counter">{{ number_format($transactionData->sum('total_amount') / 1000, 1) }}K
                            </h2>
                            {{ number_format(($transactionData->sum('total_amount') / 50000) * 100, 1) }}%
                        </div>
                        <div class="border bg-primary-subtle rounded p-3">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" width="20px" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="progress bg-primary-subtle shadow-none w-100" style="height: 6px">
                            <div class="progress-bar bg-primary" data-toggle="progress-bar" role="progressbar"
                                aria-valuenow="{{ min(100, ($transactionData->sum('total_amount') / 50000) * 100) }}"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-12 col-lg-8">
            <div class="row">
                <!-- Additional Stats Cards -->
                <div class="col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="bg-success-subtle rounded p-3">
                                    <svg class="icon-35" xmlns="http://www.w3.org/2000/svg" width="35px" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-success counter">
                                        Rp{{ number_format($transactionData->sum('total_amount') / 1000, 1) }}K</h1>
                                    <p class="text-success mb-0">Total Earning</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-itmes-center">
                                <div>
                                    <div class="p-3 rounded bg-primary-subtle">
                                        <svg class="icon-30" xmlns="http://www.w3.org/2000/svg" width="30px" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h1>{{ count($transactionData) }}</h1>
                                    <p class="mb-0">Orders Served</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Table -->
                <div class="col-md-12 col-lg-12">
                    <div class="overflow-hidden card" data-aos="fade-up" data-aos-delay="600">
                        <div class="flex-wrap card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="mb-2 card-title">Recent Orders</h4>
                                <p class="mb-0">
                                    <svg class="me-2 text-primary icon-24" width="24" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" />
                                    </svg>
                                    {{ $transactionData->count() }} total orders
                                </p>
                            </div>
                        </div>
                        <div class="p-0 card-body">
                            <div class="mt-4 table-responsive">
                                <table id="basic-table" class="table mb-0 table-striped" role="grid">
                                    <thead>
                                        <tr>
                                            <th>ORDER ID</th>
                                            <th>CUSTOMER</th>
                                            <th>DATE</th>
                                            <th>AMOUNT</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactionData->take(5) as $order)
                                            <tr>
                                                <td>#{{ $order->order_id }}</td>
                                                <td>
                                                    {{ $order->customer->first_name ?? 'N/A' }}
                                                    {{ $order->customer->last_name ?? '' }}
                                                </td>
                                                <td>{{ $order->order_date ? $order->order_date : 'N/A' }}
                                                </td>
                                                <td>Rp{{ number_format($order->total_amount, 2) }}</td>
                                                <td>
                                                    @php
    $statusColors = [
        'completed' => 'success',
        'pending' => 'warning',
        'processing' => 'info',
        'cancelled' => 'danger'
    ];
    $color = $statusColors[strtolower($order->status)] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Recent Orders Table -->
                <div class="col-md-12 col-lg-12">
                    <div class="overflow-hidden card" data-aos="fade-up" data-aos-delay="600">
                        <div class="flex-wrap card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="mb-2 card-title">Recent Customer</h4>
                                <p class="mb-0">
                                    <svg class="me-2 text-primary icon-24" width="24" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" />
                                    </svg>
                                    {{ $customerData->count() }} total orders
                                </p>
                            </div>
                        </div>
                        <div class="p-0 card-body">
                            <div class="mt-4 table-responsive">
                                <table id="basic-table" class="table mb-0 table-striped" role="grid">
                                    <thead>
                                        <tr>
                                            <th>CUSTOMER NAME</th>
                                            <th>EMAIL</th>
                                            <th>PHONE</th>
                                            <th>MEMBERSHIP</th>
                                            <th>TRANSACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customerData->take(5) as $customer)
                                            <tr>
                                                <td>
                                                    {{ $customer->first_name ?? 'N/A' }}
                                                    {{ $customer->last_name ?? '' }}
                                                </td>
                                                <td>{{ $customer->email }}</td>
                                                <td>{{ $customer->phone }}
                                                </td>
                                                <td>{{ $customer->member->membership }}</td>
                                                <td>
                                                    {{ count($customer->order) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="col-md-12 col-lg-4">
            <div class="row">
                <!-- Inventory Summary -->
                <div class="col-md-12 col-lg-12">
                    <div class="card credit-card-widget" data-aos="fade-up" data-aos-delay="900">
                        <div class="pb-4 border-0 card-header">
                            <div class="p-4 rounded primary-gradient-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="font-weight-bold">BUSINESS</h5>
                                        <p class="mb-0">PERFORMANCE OVERVIEW</p>
                                    </div>
                                    <div class="master-card-content">
                                        <svg class="master-card-1 icon-60" width="60" viewBox="0 0 24 24">
                                            <path fill="#ffffff"
                                                d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="my-4">
                                    <div class="card-number">
                                        <span class="fs-5">Total Products: {{ count($productData) }}</span>
                                        <span class="fs-5">Total Variant: {{ count($productVariantData) }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0 text-dark">Active Customers</p>
                                    <p class="mb-0">This Month</p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="text-dark">{{ count($customerData) }}</h6>
                                    <h6 class="ms-5">
                                        {{ $customerData->where('created_at', '>=', now()->subMonth())->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="flex-wrap mb-4 d-flex align-itmes-center justify-content-between">
                                <div class="d-flex align-itmes-center me-0 me-md-4">
                                    <div>
                                        <div class="p-3 mb-2 rounded bg-primary-subtle">
                                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M16.9303 7C16.9621 6.92913 16.977 6.85189 16.9739 6.77432H17C16.8882 4.10591 14.6849 2 12.0049 2C9.325 2 7.12172 4.10591 7.00989 6.77432C6.9967 6.84898 6.9967 6.92535 7.00989 7H6.93171C5.65022 7 4.28034 7.84597 3.88264 10.1201L3.1049 16.3147C2.46858 20.8629 4.81062 22 7.86853 22H16.1585C19.2075 22 21.4789 20.3535 20.9133 16.3147L20.1444 10.1201C19.676 7.90964 18.3503 7 17.0865 7H16.9303Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h5>{{ count($productData) }}</h5>
                                        <small class="mb-0">Products</small>
                                    </div>
                                </div>
                                <div class="d-flex align-itmes-center">
                                    <div>
                                        <div class="p-3 mb-2 rounded bg-secondary-subtle">
                                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M14.1213 11.2331H16.8891C17.3088 11.2331 17.6386 10.8861 17.6386 10.4677C17.6386 10.0391 17.3088 9.70236 16.8891 9.70236H14.1213C13.7016 9.70236 13.3719 10.0391 13.3719 10.4677C13.3719 10.8861 13.7016 11.2331 14.1213 11.2331ZM20.1766 5.92749C20.7861 5.92749 21.1858 6.1418 21.5855 6.61123C21.9852 7.08067 22.0551 7.7542 21.9652 8.36549L21.0159 15.06C20.8361 16.3469 19.7569 17.2949 18.4879 17.2949H7.58639C6.25742 17.2949 5.15828 16.255 5.04837 14.908L4.12908 3.7834L2.62026 3.51807C2.22057 3.44664 1.94079 3.04864 2.01073 2.64043C2.08068 2.22305 2.47038 1.94649 2.88006 2.00874L5.2632 2.3751C5.60293 2.43735 5.85274 2.72207 5.88272 3.06905L6.07257 5.35499C6.10254 5.68257 6.36234 5.92749 6.68209 5.92749H20.1766Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h5>{{ count($transactionData) }}</h5>
                                        <small class="mb-0">Transactions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="flex-wrap d-flex justify-content-between">
                                    <h2 class="mb-2">{{ count($customerData) }}</h2>
                                    <div>
                                        <span class="badge bg-success rounded-pill">Total Customers</span>
                                    </div>
                                </div>
                                <p class="text-info">Registered customers</p>
                            </div>
                        </div>
                    </div>

                    <!-- Small Stats Cards -->
                    <div class="card" data-aos="fade-up" data-aos-delay="500">
                        <div class="text-center card-body d-flex justify-content-around">
                            <div>
                                <h2 class="mb-2">{{ count($userData) }}</h2>
                                <p class="mb-0 text-gray">System Users</p>
                            </div>
                            <hr class="hr-vertial" />
                            <div>
                                <h2 class="mb-2">{{ count($membershipData) }}</h2>
                                <p class="mb-0 text-gray">Memberships Level</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="col-md-12 col-lg-12">
                    <div class="card" data-aos="fade-up" data-aos-delay="600">
                        <div class="card-body">
                            <h2 class="counter mb-3">Rp{{ number_format($transactionData->sum('total_amount'), 0) }}</h2>
                            <p class="mb-2">Total Revenue</p>
                            <h6>Monthly Growth:
                                {{ number_format(($transactionData->sum('total_amount') / 100000) * 100, 1) }}%
                            </h6>

                            <div class="mt-3">
                                <div class="pb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <p class="mb-0">Product Sales</p>
                                        <h4>{{ count($productData) }}</h4>
                                    </div>
                                    <div class="progress bg-info-subtle shadow-none w-100" style="height: 10px">
                                        <div class="progress-bar bg-info" data-toggle="progress-bar" role="progressbar"
                                            aria-valuenow="{{ min(100, (count($productData) / 50) * 100) }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="pb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <p class="mb-0">Customer Growth</p>
                                        <h4>{{ count($customerData) }}</h4>
                                    </div>
                                    <div class="progress bg-success-subtle shadow-none w-100" style="height: 10px">
                                        <div class="progress-bar bg-success" data-toggle="progress-bar" role="progressbar"
                                            aria-valuenow="{{ min(100, (count($customerData) / 200) * 100) }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="pb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <p class="mb-0">Order Completion</p>
                                        <h4>{{ $transactionData->where('status', 'completed')->count() }}</h4>
                                    </div>
                                    <div class="progress bg-primary-subtle shadow-none w-100" style="height: 10px">
                                        <div class="progress-bar bg-primary" data-toggle="progress-bar" role="progressbar"
                                            aria-valuenow="{{ $transactionData->count() > 0 ? ($transactionData->where('status', 'completed')->count() / $transactionData->count()) * 100 : 0 }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('scripts')
    <script> document.addEventListener('DOMContentLoaded', function () { // Initialize counters with animation const
            counters = document.querySelectorAll('.counter'); counters.forEach(counter => {
                const target = +counter.innerText.replace(/[^0-9.-]+/g, "");
                const increment = target / 200;
                let current = 0;

                const updateCounter = () => {
                    if (current < target) {
                        current += increment; counter.innerText = Math.ceil(current).toLocaleString();
                        setTimeout(updateCounter, 1);
                    } else { counter.innerText = target.toLocaleString(); }
                }; updateCounter();
            }); //
                Initialize progress bars const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const value = bar.getAttribute('aria-valuenow');
                bar.style.width = value + '%';
            });

            // Initialize Sales Chart
            const salesChart = new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Revenue (Rp)',
                            data: @json($chartRevenue),
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Orders',
                            data: @json($chartOrders),
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Revenue (Rp)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp' + (value / 1000).toFixed(1) + 'K';
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Orders'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label.includes('Revenue')) {
                                        return label + ': Rp' + context.parsed.y.toLocaleString();
                                    } else {
                                        return label + ': ' + context.parsed.y;
                                    }
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
