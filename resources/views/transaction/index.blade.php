@extends('layout.app')
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
                    {{-- <p>Images in Bootstrap are made responsive with <code>.img-fluid</code>.
                        <code>max-width: 100%;</code>
                        and <code>height: auto;</code> are applied to the image so that it scales with the parent element.
                    </p> --}}
                    <div class="custom-datatable-entries table-responsive mt-4">
                        <table id="datatable" data-toggle="data-table" class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Transaction Id</th>
                                    <th>Customer</th>
                                    <th>Membership</th>
                                    <th>Bill Amount</th>
                                    <th>Payment Amount</th>
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
                                        <td>{{ $item->customer->member->membership ?? 'N/A' }}</td>
                                        <td>Rp. {{ number_format($item->total_amount, '2', ',', '.') }}</td>
                                        <td>Rp. {{ number_format($item->payment->amount, '2' ,',' , '.') }}</td>
                                        <td>{{ $item->userId->username }}</td>
                                        <td>{{ $item->status }}</td>
                                        <td>{{ $item->order_date }}</td>
                                        <td>
                                            <a href="{{ route('transaction.show', $item->order_id) }}"
                                                class="btn btn-info btn-sm"> <svg class="icon-16" width="16"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M16.334 2.75H7.665C4.644 2.75 2.75 4.889 2.75 7.916V16.084C2.75 19.111 4.635 21.25 7.665 21.25H16.333C19.364 21.25 21.25 19.111 21.25 16.084V7.916C21.25 4.889 19.364 2.75 16.334 2.75Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M11.9946 16V12" stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M11.9896 8.2041H11.9996" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg> </a>
                                        </td>
                                    </tr>
                                    {{-- <div class="modal fade" id="deleteConfirm{{ $item->order_id }}"
                                        data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                        aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Warning</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Do you want to delete this data?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">No</button>
                                                    <form action="{{ route('transaction.destroy', $item->order_id) }}"
                                                        method="POST">
                                                        {{ csrf_field() }}
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Yes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
