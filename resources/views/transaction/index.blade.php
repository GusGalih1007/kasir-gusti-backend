@extends('layout.app')
@section('title', 'Transaction History')
@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
                    <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M7.67 2H16.34C19.73 2 22 4.38 22 7.92V16.091C22 19.62 19.73 22 16.34 22H7.67C4.28 22 2 19.62 2 16.091V7.92C2 4.38 4.28 2 7.67 2ZM11.43 14.99L16.18 10.24C16.52 9.9 16.52 9.35 16.18 9C15.84 8.66 15.28 8.66 14.94 9L10.81 13.13L9.06 11.38C8.72 11.04 8.16 11.04 7.82 11.38C7.48 11.72 7.48 12.27 7.82 12.62L10.2 14.99C10.37 15.16 10.59 15.24 10.81 15.24C11.04 15.24 11.26 15.16 11.43 14.99Z"
                            fill="currentColor"></path>
                    </svg>
                    <div class="d-flex justify-content-between">
                        {{ session('success') }}
                        <button type="button" class="btn-close text-right" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            @if (session('errors'))
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M7.67 1.99927H16.34C19.73 1.99927 22 4.37927 22 7.91927V16.0903C22 19.6203 19.73 21.9993 16.34 21.9993H7.67C4.28 21.9993 2 19.6203 2 16.0903V7.91927C2 4.37927 4.28 1.99927 7.67 1.99927ZM15.01 14.9993C15.35 14.6603 15.35 14.1103 15.01 13.7703L13.23 11.9903L15.01 10.2093C15.35 9.87027 15.35 9.31027 15.01 8.97027C14.67 8.62927 14.12 8.62927 13.77 8.97027L12 10.7493L10.22 8.97027C9.87 8.62927 9.32 8.62927 8.98 8.97027C8.64 9.31027 8.64 9.87027 8.98 10.2093L10.76 11.9903L8.98 13.7603C8.64 14.1103 8.64 14.6603 8.98 14.9993C9.15 15.1693 9.38 15.2603 9.6 15.2603C9.83 15.2603 10.05 15.1693 10.22 14.9993L12 13.2303L13.78 14.9993C13.95 15.1803 14.17 15.2603 14.39 15.2603C14.62 15.2603 14.84 15.1693 15.01 14.9993Z"
                            fill="currentColor"></path>
                    </svg>
                    <div>
                        {{ session('errors') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Transaction History</h4>
                    </div>
                    @if (App\Helpers\PermissionHelper::hasPermission('create'))
                    <div style="text-align: right">
                    <a href="{{ route('transaction.create') }}" class="btn btn-primary">Create</a>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    @if (App\Helpers\PermissionHelper::hasPermission('create', 'export'))
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-control" id="payment_method" name="payment_method">
                                <option value="">All Methods</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="midtrans" {{ request('payment_method') == 'midtrans' ? 'selected' : '' }}>
                                    Midtrans</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <button type="button" id="apply-filter" class="btn btn-primary">Apply Filter</button>
                            <button type="button" id="reset-filter" class="btn btn-secondary">Reset Filter</button>
                        </div>
                        <div class="col-md-6 text-end">
                            <!-- Export Buttons -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" id="export-pdf"><i
                                                class="fas fa-file-pdf"></i> PDF</a></li>
                                    <li><a class="dropdown-item" href="#" id="export-excel"><i
                                                class="fas fa-file-excel"></i> Excel</a></li>
                                    <li><a class="dropdown-item" href="#" id="export-print"><i
                                                class="fas fa-print"></i> Print</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="table-responsive custom-datatable-entries">
                        <table id="datatable" data-toggle="data-table" class="table table-hover table-bordered" style="width:100%">
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
                                        <td>{{ $item->customer->member->membership ?? 'Non-Member' }}</td>
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
                                                class="btn btn-info btn-sm">
                                                <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M16.334 2.75H7.665C4.644 2.75 2.75 4.889 2.75 7.916V16.084C2.75 19.111 4.635 21.25 7.665 21.25H16.333C19.364 21.25 21.25 19.111 21.25 16.084V7.916C21.25 4.889 19.364 2.75 16.334 2.75Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M11.9946 16V12" stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M11.9896 8.2041H11.9996" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                </svg>
                                            </a>
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

    <!-- Hidden Form for Export -->
    <form id="export-form" method="GET" action="" style="display: none;">
        <input type="hidden" name="start_date" id="export_start_date">
        <input type="hidden" name="end_date" id="export_end_date">
        <input type="hidden" name="payment_method" id="export_payment_method">
        <input type="hidden" name="status" id="export_status">
        <input type="hidden" name="export_type" id="export_type">
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#transactionTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "ordering": true,
                "searching": true,
                "responsive": true,
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    },
                    "emptyTable": "No data available in table"
                },
                "columnDefs": [{
                        "targets": [9], // Action column (index 9)
                        "orderable": false,
                        "searchable": false,
                        "className": "text-center"
                    },
                    {
                        "targets": [3, 4], // Total Bill and Total Payment columns
                        "className": "text-end"
                    },
                    {
                        "targets": [7], // Status column
                        "className": "text-center"
                    }
                ],
                "order": [
                    [8, 'desc']
                ] // Default order by Transaction Date descending
            });

            // Apply Filter
            $('#apply-filter').on('click', function() {
                applyFilters();
            });

            // Reset Filter
            $('#reset-filter').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#payment_method').val('');
                $('#status').val('');
                applyFilters();
            });

            // Enter key support for filters
            $('#start_date, #end_date, #payment_method, #status').on('keypress', function(e) {
                if (e.which === 13) {
                    applyFilters();
                }
            });

            // Export Functions - FIXED
            $('#export-pdf').on('click', function(e) {
                e.preventDefault();
                prepareExport('pdf');
            });

            $('#export-excel').on('click', function(e) {
                e.preventDefault();
                prepareExport('excel');
            });

            $('#export-print').on('click', function(e) {
                e.preventDefault();
                prepareExport('print');
            });

            function applyFilters() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const paymentMethod = $('#payment_method').val();
                const status = $('#status').val();

                let url = '{{ route('transaction.index') }}?';

                if (startDate) url += `start_date=${startDate}&`;
                if (endDate) url += `end_date=${endDate}&`;
                if (paymentMethod) url += `payment_method=${paymentMethod}&`;
                if (status) url += `status=${status}&`;

                window.location.href = url.slice(0, -1); // Remove last '&'
            }

            function prepareExport(type) {
                // Get current filter values
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const paymentMethod = $('#payment_method').val();
                const status = $('#status').val();

                // Build export URL with parameters
                let exportUrl = '';

                if (type === 'pdf') {
                    exportUrl = '{{ route('export.transactions.pdf') }}?';
                } else if (type === 'excel') {
                    exportUrl = '{{ route('export.transactions.excel') }}?';
                } else if (type === 'print') {
                    exportUrl = '{{ route('export.transactions.print') }}?';
                }

                // Add parameters to URL
                if (startDate) exportUrl += `start_date=${startDate}&`;
                if (endDate) exportUrl += `end_date=${endDate}&`;
                if (paymentMethod) exportUrl += `payment_method=${paymentMethod}&`;
                if (status) exportUrl += `status=${status}&`;

                // Remove last '&' if exists
                if (exportUrl.endsWith('&')) {
                    exportUrl = exportUrl.slice(0, -1);
                }

                // Open in new tab for PDF and Print, same tab for Excel
                if (type === 'pdf' || type === 'print') {
                    window.open(exportUrl, '_blank');
                } else if (type === 'excel') {
                    window.location.href = exportUrl;
                }
            }

            // Show active filters in table info
            function updateTableInfo() {
                const activeFilters = [];
                if ($('#start_date').val()) activeFilters.push('Start: ' + $('#start_date').val());
                if ($('#end_date').val()) activeFilters.push('End: ' + $('#end_date').val());
                if ($('#payment_method').val()) activeFilters.push('Payment: ' + $('#payment_method').val());
                if ($('#status').val()) activeFilters.push('Status: ' + $('#status').val());

                if (activeFilters.length > 0) {
                    // Remove existing filter info
                    $('.dataTables_info').find('small').remove();
                    $('.dataTables_info').append('<br><small class="text-muted">Filters: ' + activeFilters.join(
                        ', ') + '</small>');
                }
            }

            // Update table info when filters change
            $('#start_date, #end_date, #payment_method, #status').on('change', function() {
                setTimeout(updateTableInfo, 100);
            });

            // Initial call
            setTimeout(updateTableInfo, 100);
        });
    </script>

    <style>
        .dataTables_wrapper .dataTables_filter {
            float: right;
            text-align: right;
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_paginate {
            float: right;
            margin-top: 10px;
        }

        .dataTables_wrapper .dataTables_info {
            float: left;
            margin-top: 10px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: left;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
            }
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .btn-group .dropdown-menu {
            right: 0;
            left: auto;
        }
    </style>
@endsection
