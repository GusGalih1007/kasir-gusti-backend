@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Transaction Id {{ $transaction->order_id }}</h4>
                    </div>
                    <div style="text-align: right;">
                        <a href="#" class="btn btn-warning">
                            Print
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <h4 class="mb-3">Customer</h4>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Customer Name</h6>
                            <p>{{ $transaction->customer->first_name . ' ' . $transaction->customer->last_name }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Customer Membership</h6>
                            <p>{{ $transaction->customer->member->membership }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Customer E-mail</h6>
                            <p>{{ $transaction->customer->email }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Customer Phone</h6>
                            <p>{{ $transaction->customer->phone }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Customer Address</h6>
                            <p>{{ $transaction->customer->alamat }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <h4 class="mb-3">Product List</h4>
                        @foreach ($transaction['detail'] as $detail)
                        <div class="mb-3 col-sm-12 col-md-6 col-lg-6">
                            <h5 class="mb-3">{{ $loop->iteration . '. ' . $detail->product->product_name }}</h5>

                            <div class="mb-3 col-6">
                                <h6 class="mb-2">Category</h6>
                                <p>{{ $detail->product->category->name }}</p>
                            </div>

                            <div class="mb-3 col-6">
                                <h6 class="mb-2">Brand</h6>
                                <p>{{ $detail->product->brand->name }}</p>
                            </div>

                            <div class="mb-3 col-6">
                                <h6 class="mb-2">Variant</h6>
                                <p>{{ $detail->variant->variant_name }}</p>
                            </div>

                            <div class="mb-3 col-6">
                                <h6 class="mb-2">Product Quantity</h6>
                                <p>{{ $detail->quantity . ' Item'}}</p>
                            </div>

                            <div class="mb-3 col-6">
                                <h6 class="mb-2">Price Per Item</h6>
                                <p>{{ $detail->price_at_purchase }}</p>
                            </div>

                            <div class="mb-3 col-6">
                                <h6 class="mb-2">Sub-Total Price</h6>
                                <p>{{ $detail->total_price }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <h4 class="mb-3">Cashier</h4>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Username</h6>
                            <p>{{ $transaction->userId->username }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Role</h6>
                            <p>{{ $transaction->userId->role->name }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Email</h6>
                            <p>{{ $transaction->userId->email }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Phone</h6>
                            <p>{{ $transaction->userId->phone }}</p>
                        </div>

                    </div>
                    <div class="table-responsive mt-4">
                        <table id="basic-table" class="table table-bordered mb-0" role="grid">
                            <tbody>
                                <tr>
                                    <td><h5>Discount</h5></td>
                                    <td>{{ intval($transaction->discount) }}%</td>
                                </tr>
                                <tr>
                                    <td><h5>Total Price</h5></td>
                                    <td>Rp {{ number_format($transaction->total_amount, '2', ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><h5>Payment Amount</h5></td>
                                    <td>Rp {{ number_format($transaction->payment->amount, '2', ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><h5>Change</h5></td>
                                    <td>Rp {{ number_format($transaction->change, '2', ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
