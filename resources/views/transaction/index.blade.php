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

                    <div class="table-responsive">
                        <table id="transactionTable" class="table table-hover table-bordered" style="width:100%">
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
