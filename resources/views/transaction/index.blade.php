@extends('layout.app')
@section('title', 'Transaction List')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Transaction History</h4>
                    </div>
                    <div style="text-align: right;">
                        <a href="{{ route('transaction.create') }}" class="btn btn-primary">
                            Create
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="custom-datatable-entries table-responsive mt-4">
                        <table id="datatable" data-toggle="data-table" class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Transaction Id</th>
                                    <th>Customer</th>
                                    <th>Membership</th>
                                    <th>Bill Amount</th>
                                    <th>Payment Method</th>
                                    <th>Payment Status</th>
                                    <th>Cashier</th>
                                    <th>Transaction Status</th>
                                    <th>Transaction Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->order_id }}</td>
                                        <td>
                                            @if ($item->customer)
                                                {{ $item->customer->first_name . ' ' . $item->customer->last_name }}
                                            @else
                                                Walk-in Customer
                                            @endif
                                        </td>
                                        <td>{{ $item->customer->member->membership ?? 'N/A' }}</td>
                                        <td>Rp. {{ number_format($item->total_amount, 2, ',', '.') }}</td>
                                        <td>
                                            <span
                                                class="badge 
                                                @if ($item->payment->payment_method == 'Midtrans') bg-info
                                                @elseif($item->payment->payment_method == 'Cash') bg-success
                                                @elseif($item->payment->payment_method == 'Credit Card') bg-warning
                                                @else bg-secondary @endif">
                                                {{ $item->payment->payment_method }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge 
                                                @if ($item->payment->status == 'complete') bg-success
                                                @elseif($item->payment->status == 'pending') bg-warning
                                                @elseif($item->payment->status == 'failed') bg-danger
                                                @else bg-secondary @endif">
                                                {{ ucfirst($item->payment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $item->userId->username }}</td>
                                        <td>
                                            <span
                                                class="badge 
                                                @if ($item->status == 'completed') bg-success
                                                @elseif($item->status == 'pending') bg-warning
                                                @elseif($item->status == 'failed') bg-danger
                                                @else bg-secondary @endif">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $item->order_date }}</td>
                                        <td style="text-align: center">
                                            <a href="{{ route('transaction.show', $item->order_id) }}"
                                                class="btn btn-info btn-sm" title="View Details">
                                                <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M16.334 2.75H7.665C4.644 2.75 2.75 4.889 2.75 7.916V16.084C2.75 19.111 4.635 21.25 7.665 21.25H16.333C19.364 21.25 21.25 19.111 21.25 16.084V7.916C21.25 4.889 19.364 2.75 16.334 2.75Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M11.9946 16V12" stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M11.9896 8.2041H11.9996" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </a>

                                            @if ($item->payment->payment_method == 'Midtrans' && in_array($item->status, ['pending']))
                                                {{-- Retry/Continue Payment Button --}}
                                                <a href="{{ route('transaction.payment-url', $item->order_id) }}"
                                                    class="btn btn-{{ $item->status == 'pending' ? 'success' : 'warning' }} btn-sm"
                                                    title="{{ $item->status == 'pending' ? 'Continue Payment' : 'Retry Payment' }}">
                                                    @if ($item->status == 'pending')
                                                        💳
                                                    @else
                                                        🔄
                                                    @endif
                                                </a>
                                                @if ($item->status == ['pending', 'replaced'])
                                                {{-- Status Check Button --}}
                                                <a href="{{ route('transaction.check-status', $item->order_id) }}"
                                                    class="btn btn-{{ $item->status == 'pending' ? 'primary' : 'info' }} btn-sm"
                                                    title="Check Status">
                                                    🔍
                                                </a>
                                                @endif
                                            @endif
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
