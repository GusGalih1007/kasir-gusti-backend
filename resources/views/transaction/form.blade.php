@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header d-flex justify-content-between align-items-center text-white">
                    <h4 class="card-title mb-0" id="card-title">Create Transaction</h4>
                </div>
                <div class="card-body p-4">

                    <form id="transaction-form" class="row" method="POST" action="{{ route('transaction.store') }}">
                        @csrf

                        {{-- CUSTOMER SELECTION --}}
                        <div class="col-md-12" id="customer">
                            <label for="customer_id" class="form-label">Customer</label>
                            <div class="input-group">
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="" selected hidden>Select Customer</option>
                                    @foreach ($customer as $c)
                                        <option value="{{ $c->customer_id }}">{{ $c->first_name }} {{ $c->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#addCustomerModal">
                                    <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.0001 8.32739V15.6537" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M15.6668 11.9904H8.3335" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M16.6857 2H7.31429C4.04762 2 2 4.31208 2 7.58516V16.4148C2 19.6879 4.0381 22 7.31429 22H16.6857C19.9619 22 22 19.6879 22 16.4148V7.58516C22 4.31208 19.9619 2 16.6857 2Z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>


                        {{-- HIDDEN CURRENCY --}}
                        <input type="hidden" name="currency" value="IDR">

                        <input type="hidden" name="product" id="product_json">


                        {{-- CATEGORY FILTER --}}
                        <div class="col-md-6 form-group">
                            <label for="category_select" class="form-label fw-bold">Category</label>
                            <select id="category_select" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories->where('parent_id', null) as $cat)
                                    <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="subcategory_select" class="form-label fw-bold">Subcategory</label>
                            <select id="subcategory_select" class="form-select" disabled>
                                <option value="">-- Pilih Subkategori --</option>
                            </select>
                        </div>

                        {{-- PRODUCT AND VARIANT --}}
                        <div class="col-md-4 form-group">
                            <label for="product_select" class="form-label fw-bold">Product</label>
                            <select id="product_select" class="form-select">
                                <option value="">-- Pilih Product --</option>
                                @foreach ($product as $p)
                                    <option value="{{ $p->product_id }}" data-category="{{ $p->category_id }}">
                                        {{ $p->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="variant_select" class="form-label fw-bold">Variant</label>
                            <select id="variant_select" class="form-select" disabled>
                                <option value="">Pilih variant setelah product</option>
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="stock_display" class="form-label fw-bold">Available</label>
                            <input type="text" id="stock_display" class="form-control" value="0" readonly>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="qty_input" class="form-label fw-bold">Quantity</label>
                            <input type="number" id="qty_input" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-12">
                            <button type="button" id="add_product_btn" class="btn btn-success col-12">
                                + Tambah Item
                            </button>
                        </div>

                        {{-- CART TABLE --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-striped align-middle" id="cart-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        {{-- TOTALS --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Total</label>
                                <input type="text" id="total_amount" class="form-control money" readonly
                                    value="Rp 0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Discount</label>
                                <input type="text" id="discount_amount" class="form-control money" readonly
                                    value="Rp 0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Total Due</label>
                                <input type="text" id="total_due" class="form-control money" readonly value="Rp 0">
                            </div>
                        </div>

                        {{-- PAYMENT --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label fw-bold">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="">Pilih Metode Pembayaran</option>
                                    <option value="Cash">Tunai</option>
                                    <option value="Midtrans">Online Payment (Midtrans)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_input" name="payment" class="form-label fw-bold">Pay (Rp)</label>
                                <input type="text" id="payment_input" class="form-control money"
                                    placeholder="Masukkan jumlah pembayaran">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="change_output" class="form-label fw-bold">Change</label>
                            <input type="text" id="change_output" class="form-control money" readonly value="Rp 0">
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary px-5">💾 Simpan</button>
                            <a href="{{ route('transaction.index') }}" class="btn btn-outline-secondary px-5">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Add Customer Modal -->
            <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="addCustomerLabel">Add New Customer</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addCustomerForm" class="needs-validation row g-3" novalidate>
                                @csrf

                                <div class="col-4 form-group">
                                    <label for="customerFirstName" class="form-label">Firstname</label>
                                    <input type="text" name="first_name" class="form-control" id="customerFirstName"
                                        required>
                                    <div class="invalid-feedback">Firstname is Required</div>
                                </div>

                                <div class="col-4 form-group">
                                    <label for="customerLastName" class="form-label">Lastname</label>
                                    <input type="text" name="last_name" class="form-control" id="customerLastName"
                                        required>
                                    <div class="invalid-feedback">Lastname is Required</div>
                                </div>

                                <div class="col-4 form-group">
                                    <label for="customerPhone" class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" id="customerPhone"
                                        required>
                                    <div class="invalid-feedback">Phone is Required</div>
                                </div>

                                <div class="col-6 form-group">
                                    <label for="customerEmail" class="form-label">E-Mail</label>
                                    <input type="email" name="email" class="form-control" id="customerEmail"
                                        required>
                                    <div class="invalid-feedback">Email is Required</div>
                                </div>

                                <div class="col-6 form-group">
                                    <label class="form-label" for="membership">Membership</label>
                                    <select name="membership" class="form-select" id="membership">
                                        <option value="" selected hidden>Select customer membership</option>
                                        @foreach ($membership as $item)
                                            <option value="{{ $item->membership_id }}">{{ $item->membership }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 form-group">
                                    <label class="form-label" for="alamat">Alamat</label>
                                    <textarea class="form-control" name="alamat" id="alamat" rows="4" required></textarea>
                                    <div class="invalid-feedback">Alamat is required</div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="saveCustomerBtn" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Save Customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL FOR INSUFFICIENT FUNDS --}}
    <div class="modal fade" id="insufficientModal" tabindex="-1" aria-labelledby="insufficientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="insufficientModalLabel">⚠️ Dana Tidak Cukup</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center fs-5">
                    Jumlah pembayaran tidak mencukupi total yang harus dibayar.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@php
    $variantData = $variant->map(
        fn($v) => [
            'variant_id' => $v->variant_id,
            'product_id' => $v->product_id,
            'variant_name' => $v->variant_name,
            'price' => $v->price,
            'stock_qty' => $v->stock_qty,
        ],
    );
    $categoryData = $categories;
@endphp

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function() {
        const variants = @json($variantData);
        const categories = @json($categoryData);
        let productList = [];
        let discountPercent = 0;
        let customerIsMember = false;

        const formatRupiah = num => 'Rp ' + (num || 0).toLocaleString('id-ID');

        // ===== CATEGORY & SUBCATEGORY FILTER =====
        $('#category_select').on('change', function() {
            const parentId = $(this).val();
            const subSelect = $('#subcategory_select');
            subSelect.empty().append('<option value="">-- Pilih Subkategori --</option>');
            if (!parentId) {
                subSelect.prop('disabled', true);
                return;
            }
            const subs = categories.filter(c => c.parent_id == parentId);
            if (subs.length) {
                subs.forEach(s => subSelect.append(
                    `<option value="${s.category_id}">${s.name}</option>`));
                subSelect.prop('disabled', false);
            } else subSelect.prop('disabled', true);
        });

        // ===== PRODUCT FILTER BASED ON CATEGORY =====
        $('#subcategory_select, #category_select').on('change', function() {
            const catId = $('#subcategory_select').val() || $('#category_select').val();
            $('#product_select option').each(function() {
                const productCat = $(this).data('category');
                $(this).toggle(!catId || productCat == catId || $(this).val() === '');
            });
        });

        // ===== VARIANT LOAD =====
        $('#product_select').on('change', function() {
            const pid = $(this).val();
            const varSel = $('#variant_select');
            varSel.empty().append('<option value="">-- Pilih Variant --</option>');
            $('#stock_display').val(0);
            $('#add_product_btn').prop('disabled', true);

            if (!pid) {
                varSel.prop('disabled', true);
                return;
            }

            const related = variants.filter(v => v.product_id == pid);
            related.forEach(v => {
                varSel.append(`
      <option value="${v.variant_id}" data-price="${v.price}" data-stock="${v.stock_qty}">
        ${v.variant_name} — Stok: ${v.stock_qty}
      </option>
    `);
            });

            varSel.prop('disabled', false);
        });

        $('#variant_select').on('change', function() {
            const stock = $('#variant_select option:selected').data('stock') || 0;
            $('#stock_display').val(stock);

            validateAddButton();
        });


        // ===== ADD PRODUCT =====
        $('#add_product_btn').on('click', function() {
            const pid = $('#product_select').val();
            const pname = $('#product_select option:selected').text();
            const vid = $('#variant_select').val();
            const vname = $('#variant_select option:selected').text();
            const price = parseFloat($('#variant_select option:selected').data('price')) || 0;
            const stock = parseInt($('#variant_select option:selected').data('stock')) || 0;
            const qty = parseInt($('#qty_input').val()) || 1;
            if (!pid || !vid) return alert('Select product and variant first');
            if (stock <= 0) return alert('Stock is empty');
            if (qty > stock) return alert('Quantity cannot be more than available stock');

            const exist = productList.find(p => p.variant_id == vid);
            if (exist) exist.qty += qty;
            else productList.push({
                product_id: pid,
                variant_id: vid,
                product_name: pname,
                variant_name: vname,
                price,
                stock,
                qty
            });

            renderTable();
            calculateTotals();
        });

        function renderTable() {
            const tb = $('#cart-table tbody');
            tb.empty();
            productList.forEach((item, i) => {
                const total = item.qty * item.price;
                tb.append(`
        <tr>
          <td>${item.product_name}</td>
          <td>${item.variant_name}</td>
          <td>${formatRupiah(item.price)}</td>
          <td>${item.stock}</td>
          <td>
            <button class="btn btn-sm btn-outline-secondary decrease" data-id="${item.variant_id}">-</button>
            ${item.qty}
            <button class="btn btn-sm btn-outline-secondary increase" data-id="${item.variant_id}">+</button>
          </td>
          <td>${formatRupiah(total)}</td>
          <td><button class="btn btn-sm btn-danger remove" data-id="${item.variant_id}">x</button></td>
        </tr>
      `);
            });
        }

        $(document).on('click', '.increase', function() {
            const id = $(this).data('id');
            const it = productList.find(p => p.variant_id == id);
            if (it && it.qty < it.stock) {
                it.qty++;
                renderTable();
                calculateTotals();
            } else alert('Stok tidak cukup');
        });
        $(document).on('click', '.decrease', function() {
            const id = $(this).data('id');
            const it = productList.find(p => p.variant_id == id);
            if (it && it.qty > 1) {
                it.qty--;
                renderTable();
                calculateTotals();
            }
        });
        $(document).on('click', '.remove', function() {
            const id = $(this).data('id');
            productList = productList.filter(p => p.variant_id != id);
            renderTable();
            calculateTotals();
        });

        // ===== CUSTOMER MEMBERSHIP =====
        $('#customer_id').on('change', function() {
            const opt = $(this).find(':selected');
            customerIsMember = opt.data('member') == 1;
            discountPercent = parseFloat(opt.data('discount')) || 0;
            calculateTotals();
        });

        // ===== CURRENCY INPUT HANDLING =====
        $(document).on('input', '.money', function() {
            let val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(formatRupiah(parseInt(val || 0)));
        });

        function parseRupiah(str) {
            return parseInt(str.replace(/[^0-9]/g, '')) || 0;
        }

        // ===== TOTALS =====
        function calculateTotals() {
            let total = productList.reduce((s, i) => s + (i.price * i.qty), 0);
            let disc = customerIsMember ? (total * discountPercent / 100) : 0;
            let due = total - disc;
            $('#total_amount').val(formatRupiah(total));
            $('#discount_amount').val(formatRupiah(disc));
            $('#total_due').val(formatRupiah(due));
            calcChange();
        }

        // ===== CHANGE CALCULATION =====
        $('#payment_input').on('input', calcChange);

        function calcChange() {
            const pay = parseRupiah($('#payment_input').val());
            const due = parseRupiah($('#total_due').val());
            const change = pay - due;
            $('#change_output').val(formatRupiah(change > 0 ? change : 0));
        }

        $(document).ready(function() {
            // Initialize payment method state
            $('#payment_method').trigger('change');
            $('#saveCustomerBtn').on('click', function() {
                let form = $('#addCustomerForm');
                if (!form[0].checkValidity()) {
                    form.addClass('was-validated');
                    return;
                }

                let formData = form.serialize();

                $.ajax({
                    url: "{{ route('customer.store') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#addCustomerModal').modal('hide');
                        form.trigger('reset');
                        form.removeClass('was-validated');

                        // Add new customer to dropdown
                        let newCustomer = response
                            .data; // Make sure your controller returns 'data' key
                        $('#customer_id').append(
                            `<option value="${newCustomer.customer_id}" selected>
                        ${newCustomer.first_name} ${newCustomer.last_name}
                    </option>`
                        );

                        // Optional: small notification
                        $('<div class="alert alert-success mt-2 text-center">Customer added successfully!</div>')
                            .insertAfter('#card-title')
                            .delay(2000).fadeOut(500, function() {
                                $(this).remove();
                            });
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message ||
                            'Failed to add customer.';
                        alert(msg);
                    }
                });
            });
        });

        // ===== PAYMENT METHOD HANDLING =====
        $('#payment_method').on('change', function() {
            const method = $(this).val();
            const paymentInputDiv = $('#payment_input').closest('.col-md-6');
            const changeOutputDiv = $('#change_output').closest('.mb-4');

            if (method === 'Midtrans') {
                // Hide payment input and change output for Midtrans
                paymentInputDiv.hide();
                changeOutputDiv.hide();

                // Clear and disable payment input
                $('#payment_input').val('Rp 0').prop('disabled', true);
                $('#change_output').val('Rp 0');
            } else {
                // Show payment input and change output for other methods
                paymentInputDiv.show();
                changeOutputDiv.show();

                // Enable payment input and recalculate
                $('#payment_input').prop('disabled', false);
                calcChange();
            }
        });

        // ===== SUBMIT FORM =====
        $('#transaction-form').on('submit', function(e) {
            e.preventDefault();

            const paymentMethod = $('#payment_method').val();
            const due = parseRupiah($('#total_due').val());

            // Validate payment method
            if (!paymentMethod) {
                alert('Please select payment method');
                return;
            }

            // For Midtrans - skip payment amount validation
            if (paymentMethod === 'Midtrans') {
                // 🟢 Prepare data for submission
                $('#product_json').val(JSON.stringify(productList));

                // 🟢 Set payment amount to total due for Midtrans
                $('<input>').attr({
                    type: 'hidden',
                    name: 'payment',
                    value: due
                }).appendTo('#transaction-form');

                // Submit the form
                this.submit();
                return;
            }

            // For other payment methods - validate payment amount
            const pay = parseRupiah($('#payment_input').val());

            if (pay < due) {
                const modal = new bootstrap.Modal(document.getElementById('insufficientModal'));
                modal.show();
                return;
            }

            // 🟢 Prepare data for regular payment submission
            $('#product_json').val(JSON.stringify(productList));

            // 🟢 Add payment amount to form
            $('<input>').attr({
                type: 'hidden',
                name: 'payment',
                value: pay
            }).appendTo('#transaction-form');

            this.submit();
        });

        function validateAddButton() {
            const pid = $('#product_select').val();
            const vid = $('#variant_select').val();
            const qty = parseInt($('#qty_input').val()) || 0;
            const stock = parseInt($('#stock_display').val()) || 0;

            const canAdd = pid && vid && qty > 0 && qty <= stock;
            $('#add_product_btn').prop('disabled', !canAdd);
            $('#qty_input').on('input', validateAddButton);
            $('#product_select').on('change', validateAddButton);
            $('#variant_select').on('change', validateAddButton);
        }

    });
</script>
