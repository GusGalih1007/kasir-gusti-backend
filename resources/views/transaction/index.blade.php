@extends('layout.app')
@section('title', 'Transaction History')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Transaction History</h4>
                    </div>
                    <div style="text-align: right">
                        <a href="{{ route('transaction.create') }}" class="btn btn-primary">Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="custom-datatable-entries table-responsive">
                        <table id="datatable" data-toggle="data-table" class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Customer Name</th>
                                    <th>Customer Membership</th>
                                    <th>Total Bill</th>
                                    <th>Total Payment</th>
                                    <th>Payment Method</th>
                                    <th>Cashier</th>
                                    <th>Status</th>
                                    <th>Transaction Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->order_id }}</td>
                                        <td>{{ $item->customer->first_name . ' ' . $item->customer->last_name }}</td>
                                        <td>{{ $item->customer->member->membership }}</td>
                                        <td>{{ 'Rp. ' . number_format($item->total_amount, 2, ',', '.') }}</td>
                                        <td>{{ 'Rp. ' . number_format($item->payment->amount, 2, ',', '.') }}</td>
                                        <td>{{ ucfirst($item->payment->payment_method) }}</td>
                                        <td>{{ $item->userId->username }}</td>
                                        <td style="text-align: center">
                                            @switch($item->status)
                                                @case('completed')
                                                    <span class="badge rounded-pill bg-success">Complete</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge rounded-pill bg-warning">Pending</span>
                                                    @break
                                                @default
                                                    <span class="badge rounded-pill bg-danger">Failed</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $item->order_date }}</td>
                                        <td>
                                            <a href="{{ route('transaction.show', $item->order_id) }}"
                                                class="btn btn-warning btn-sm"> <svg class="icon-16" width="16"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M15.1655 4.60254L19.7315 9.16854" stroke="currentColor"
                                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                </svg> </a>
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
@endsection