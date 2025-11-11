@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Create Transaction</h4>
                    </div>
                </div>
                <div class="card-body">

                    <form id="transaction-form" method="POST" class="row" action="{{ route('transaction.store') }}">
                        {{ csrf_field() }}

                        <div class="col-12 form-group">
                            <label for="customer_id">Customer (Optional)</label>
                            <select name="customer_id" id="customer_id" class="form-control">
                                <option value="">-- Pilih Customer --</option>
                                @foreach ($customer as $c)
                                    @php
                                        // membership might be relation; try to get first membership if exists
                                        $m = $c->membership()->first();
                                        $discountPercent = $m ? $m->discount : 0;
                                    @endphp
                                    <option value="{{ $c->customer_id }}" data-is-member="{{ $c->is_member ? 1 : 0 }}"
                                        data-membership-discount="{{ $discountPercent }}">
                                        {{ $c->first_name }} {{ $c->last_name }} {{ $c->is_member ? ' | Member' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <label for="currency">Currency</label>
                            <select name="currency" id="currency" class="form-control" required>
                                <option value="IDR">IDR</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <label for="payment_method">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="Cash">Tunai</option>
                                <option value="Credit Card">Kartu Kredit</option>
                                <option value="Transfer">Transfer Bank</option>
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <label for="product_select">Product</label>
                            <select id="product_select" class="form-control">
                                <option value="">-- Pilih Product --</option>
                                @foreach ($product as $p)
                                    <option value="{{ $p->product_id }}" data-name="{{ $p->product_name }}">
                                        {{ $p->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-6 col-6">
                            <label for="variant_select">Variant</label>
                            <select id="variant_select" class="form-control" disabled>
                                <option value="">Pilih variant setelah memilih product</option>
                                {{-- variants will be populated by JS --}}
                            </select>
                        </div>

                        <div class="form-row align-items-end">

                            <div class="form-group col-md-2">
                                <label for="qty_input">Qty</label>
                                <input type="number" id="qty_input" class="form-control" value="1" min="1">
                            </div>

                            <div class="form-group col-md-4">
                                <button type="button" id="add_product_btn" class="btn btn-primary btn-block">Tambah
                                    Item</button>
                            </div>
                        </div>

                        {{-- Table for added products --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mt-3" id="cart-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                            <div class="form-group col-6">
                                <label for="total_amount">Total Amount</label>
                                <input type="number" name="total_amount" id="total_amount" class="form-control" readonly
                                    value="0">
                            </div>

                            <div class="form-group col-6">
                                <label for="discount_amount">Discount (Rp)</label>
                                <input type="number" name="discount" id="discount_amount" class="form-control" readonly
                                    value="0">
                            </div>

                            <div class="form-group col-12" id="total_due_field" style="display:none;">
                                <label for="total_due">Total to be Paid</label>
                                <input type="number" name="total_due" id="total_due" class="form-control" readonly
                                    value="0">
                            </div>

                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="payment_input">Pay</label>
                                <input type="number" name="payment" id="payment_input" class="form-control" required>
                                <div class="invalid-feedback" id="payment_error"></div>
                            </div>

                            <div class="form-group col-12">
                                <label for="change_output">Change</label>
                                <input type="number" name="change" id="change_output" class="form-control" readonly
                                    value="0">
                            </div>
                        </div>

                        {{-- hidden input to send product list as JSON --}}
                        <input type="hidden" name="product" id="product_payload">

                        <div id="form_alerts" class="mt-2"></div>

                        <button type="submit" class="btn btn-success">Save Transaction</button>
                        <a href="{{ route('transaction.index') }}" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@php
    $variantData = $variant->map(function ($v) {
        return [
            'variant_id' => $v->variant_id,
            'product_id' => $v->product_id,
            'variant_name' => $v->variant_name,
            'price' => $v->price,
            'stock_qty' => $v->stock_qty,
        ];
    });
@endphp

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const variants = @json($variantData);
        let productList = [];
        let discountPercent = 0;
        let customerIsMember = false;

        // When customer changes
        $('#customer_id').on('change', function() {
            const selectedOption = $(this).find(':selected');
            customerIsMember = selectedOption.data('is-member') == 1;
            discountPercent = parseFloat(selectedOption.data('membership-discount')) || 0;
            calculateTotals();
        });

        // Update variant dropdown when product changes
        $('#product_select').on('change', function() {
            const productId = $(this).val();
            const variantSelect = $('#variant_select');
            variantSelect.empty().append('<option value="">-- Pilih Variant --</option>');

            const filteredVariants = variants.filter(v => v.product_id == productId);
            filteredVariants.forEach(v => {
                variantSelect.append(
                    `<option value="${v.variant_id}" data-price="${v.price}" data-stock="${v.stock_qty}">
                    ${v.variant_name} - Rp${v.price.toLocaleString()} (Stock: ${v.stock_qty})
                </option>`
                );
            });

            variantSelect.prop('disabled', filteredVariants.length === 0);
        });

        // Add product to cart
        $('#add_product_btn').on('click', function() {
            const productId = $('#product_select').val();
            const variantId = $('#variant_select').val();
            const productName = $('#product_select option:selected').text();
            const variantName = $('#variant_select option:selected').text();
            const price = parseFloat($('#variant_select option:selected').data('price'));
            const stock = parseInt($('#variant_select option:selected').data('stock'));
            const qty = parseInt($('#qty_input').val()) || 1;

            if (!productId || !variantId) {
                alert('Please select a product and variant.');
                return;
            }

            const existing = productList.find(p => p.variant_id == variantId);
            if (existing) {
                existing.qty += qty;
            } else {
                productList.push({
                    product_id: productId,
                    variant_id: variantId,
                    product_name: productName,
                    variant_name: variantName,
                    price: price,
                    qty: qty,
                    stock: stock
                });
            }

            updateTable();
            calculateTotals();
        });

        // Update table display
        function updateTable() {
            const tbody = $('#cart-table tbody');
            tbody.empty();

            productList.forEach((item) => {
                const total = item.qty * item.price;
                tbody.append(`
                <tr>
                    <td>${item.product_name}</td>
                    <td>${item.variant_name}</td>
                    <td>Rp${item.price.toLocaleString()}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary decrease" data-id="${item.variant_id}">-</button>
                        ${item.qty}
                        <button class="btn btn-sm btn-secondary increase" data-id="${item.variant_id}">+</button>
                    </td>
                    <td>Rp${total.toLocaleString()}</td>
                    <td><button class="btn btn-sm btn-danger remove" data-id="${item.variant_id}">Remove</button></td>
                </tr>
            `);
            });

            $('#product_payload').val(JSON.stringify(productList));
        }

        // Increase quantity
        $(document).on('click', '.increase', function() {
            const id = $(this).data('id');
            const item = productList.find(p => p.variant_id == id);
            if (item && item.qty < item.stock) {
                item.qty++;
                updateTable();
                calculateTotals();
            } else {
                alert('Not enough stock.');
            }
        });

        // Decrease quantity
        $(document).on('click', '.decrease', function() {
            const id = $(this).data('id');
            const item = productList.find(p => p.variant_id == id);
            if (item && item.qty > 1) {
                item.qty--;
                updateTable();
                calculateTotals();
            }
        });

        // Remove item
        $(document).on('click', '.remove', function() {
            const id = $(this).data('id');
            productList = productList.filter(p => p.variant_id != id);
            updateTable();
            calculateTotals();
        });

        // Calculate totals
        function calculateTotals() {
            let total = productList.reduce((sum, item) => sum + (item.price * item.qty), 0);
            let discount = customerIsMember ? (total * discountPercent / 100) : 0;
            let totalDue = total - discount;

            $('#total_amount').val(total);
            $('#discount_amount').val(discount);
            $('#total_due').val(totalDue);
            $('#total_due_field').show();
            calculateChange();
        }

        // Calculate change
        $('#payment_input').on('input', calculateChange);

        function calculateChange() {
            const payment = parseFloat($('#payment_input').val()) || 0;
            const totalDue = parseFloat($('#total_due').val()) || 0;
            const change = payment - totalDue;
            $('#change_output').val(change >= 0 ? change : 0);
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Submit form via AJAX
        $(document).ready(function() {
            $('#orderForm').submit(function(e) {
                e.preventDefault();

                // Serialize the form
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('transaction.store') }}", // your normal Laravel route
                    method: "POST",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        $('#submitBtn').prop('disabled', true).text(
                            'Processing...');
                    },
                    success: function(response) {
                        alert(response.message || 'Order successfully created!');
                        $('#orderForm')[0].reset();
                        $('#submitBtn').prop('disabled', false).text('Submit');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        let res = xhr.responseJSON;
                        let message = res?.error ||
                            'Something went wrong. Please try again.';
                        alert(message);
                        $('#submitBtn').prop('disabled', false).text('Submit');
                    }
                });
            });
        });
    });
</script>
