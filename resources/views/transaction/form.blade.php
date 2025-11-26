@extends('layout.app')
@section('title', 'Transaction Form')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Transaction Form</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('transaction.store') }}" method="POST" class="row g-3" novalidate>
                        {{ csrf_field() }}

                        <!-- Customer Selection -->
                        <div class="form-group col-12">
                            <label for="customerId" class="form-label">Customer</label>
                            <div class="input-group">
                                <select name="customer_id" class="form-select" id="customerId" required>
                                    <option value="" hidden selected>Select Customer</option>
                                    @foreach ($customer as $item)
                                        <option value="{{ $item->customer_id }}" data-member="{{ $item->is_member }}"
                                            data-discount="{{ $item->member->discount ?? 0 }}">
                                            {{ $item->first_name }} {{ $item->last_name }}
                                            @if ($item->is_member)
                                                (Member - {{ $item->member->discount ?? 0 }}% Discount)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#createCustomer">
                                    <i class="fas fa-plus"></i> Add Customer
                                </button>
                            </div>
                            <div id="discountInfo" class="form-text text-success d-none"></div>
                        </div>

                        <!-- Product Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="categoryId" class="form-label">Product Category</label>
                                <select name="category_id" id="categoryId" class="form-select" required>
                                    <option value="" hidden selected>Select Product Category</option>
                                    @foreach ($category as $item)
                                        <option value="{{ $item->category_id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="productId" class="form-label">Product</label>
                                <select name="product_id" id="productId" class="form-select" disabled required>
                                    <option value="" hidden selected>Select Product to buy</option>
                                </select>
                            </div>
                        </div>

                        <!-- Variant Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="variantId" class="form-label">Product Variant</label>
                                <select name="variant_id" id="variantId" class="form-select" disabled required>
                                    <option value="" hidden selected>Select Product variant to buy</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="stockQty" class="form-label">Available Stock</label>
                                <input type="number" class="form-control" name="stock_qty" id="stockQty" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" name="quantity" id="quantity" min="1"
                                    value="1">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="button" id="addItem" class="btn btn-success">Add Item</button>
                        </div>

                        <!-- Product List Table -->
                        <div class="table-responsive mt-4 col-12">
                            <table id="productList" class="table table-striped mb-0" role="grid">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Price per-item</th>
                                        <th>Quantity</th>
                                        <th>Sub-total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- Summary Section -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <label for="totalAmount" class="form-label">Total Amount</label>
                                <input type="number" class="form-control" name="totalAmount" id="totalAmount" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="discount" class="form-label">Discount</label>
                                <input type="number" class="form-control" name="discount" id="discount" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="totalAfterDiscount" class="form-label">Total After Discount</label>
                                <input type="number" class="form-control" name="totalAfterDiscount" id="totalAfterDiscount"
                                    readonly>
                            </div>
                        </div>

                        <!-- Payment Section -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="paymentMethod" class="form-label">Payment Method</label>
                                <select name="payment_method" id="paymentMethod" class="form-select" required>
                                    <option value="" hidden selected>Select payment method</option>
                                    <option value="cash">Cash</option>
                                    <option value="midtrans">Online (Midtrans)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="payment" class="form-label">Payment Amount</label>
                                <input type="number" class="form-control" name="payment" id="payment" min="0"
                                    required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="change" class="form-label">Change</label>
                                <input type="number" class="form-control" name="change" id="change" readonly>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary">Submit Transaction</button>
                            <a href="{{ url()->previous() }}" class="btn btn-light">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Customer Modal -->
    <div class="modal fade" id="createCustomer" tabindex="-1" aria-labelledby="createCustomerLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCustomerLabel">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="customerForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name" id="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" id="last_name">
                            </div>

                            <!-- Alamat Fields -->
                            <div class="col-md-6">
                                <label for="provinsi" class="form-label">Provinsi <span
                                        class="text-danger">*</span></label>
                                <select name="provinsi" id="modal_provinsi" class="form-select" required>
                                    <option value="" hidden selected>Pilih Provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="kota" class="form-label">Kota <span class="text-danger">*</span></label>
                                <select name="kota" id="modal_kota" class="form-select" required>
                                    <option value="" hidden selected>Pilih Kota</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="kecamatan" class="form-label">Kecamatan <span
                                        class="text-danger">*</span></label>
                                <select name="kecamatan" id="modal_kecamatan" class="form-select" required>
                                    <option value="" hidden selected>Pilih Kecamatan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="desa" class="form-label">Desa <span class="text-danger">*</span></label>
                                <select name="desa" id="modal_desa" class="form-select" required>
                                    <option value="" hidden selected>Pilih Desa</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="alamat" class="form-label">Alamat Lengkap <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="alamat" id="alamat" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="phone" id="phone" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_member" id="is_member">
                                    <label class="form-check-label" for="is_member">
                                        Make this customer a member
                                    </label>
                                </div>
                            </div>
                            <div class="col-12" id="membershipField" style="display: none;">
                                <label for="membership" class="form-label">Membership Type</label>
                                <select name="membership" id="membership_id" class="form-select">
                                    <option value="" selected>Select Membership Type</option>
                                    @foreach ($membership as $item)
                                        <option value="{{ $item->membership_id }}"
                                            data-discount="{{ $item->discount }}">
                                            {{ $item->membership }} ({{ $item->discount }}% Discount)
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text" id="membershipInfo"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            let productList = [];
            let discountPercent = 0;

            // ==================== CUSTOMER MODAL SPECIFIC CODE ====================

            // Toggle membership field based on checkbox
            $('#is_member').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#membershipField').slideDown();
                } else {
                    $('#membershipField').slideUp();
                    $('#membership_id').val('');
                    $('#membershipInfo').text('');
                }
            });

            // Show membership info when selection changes
            $('#membership_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const discount = selectedOption.data('discount') || 0;
                if (discount > 0) {
                    $('#membershipInfo').html(
                        `<span class="text-success">This membership includes ${discount}% discount on all purchases</span>`
                        );
                } else {
                    $('#membershipInfo').html(
                    `<span class="text-info">Standard membership selected</span>`);
                }
            });

            // Customer Modal Form Submission
            $('#customerForm').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Customer form submission started');

                // Validasi form customer
                const form = $(this)[0];
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // Prepare form data
                const formData = $(this).serialize();

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

                $.ajax({
                    url: '{{ route('customer.store') }}',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        console.log('Customer saved successfully:', response);

                        // Reset button state
                        submitBtn.html(originalText).prop('disabled', false);

                        // Add new customer to dropdown
                        const customerName = response.data.first_name + ' ' + (response.data
                            .last_name || '');
                        const memberText = response.data.is_member ?
                            ` (Member - ${response.data.member?.discount || 0}% Discount)` : '';

                        const newOption = new Option(
                            customerName + memberText,
                            response.data.customer_id,
                            false,
                            false
                        );

                        // Add data attributes
                        $(newOption).attr('data-member', response.data.is_member ? '1' : '0')
                            .attr('data-discount', response.data.member?.discount || 0);

                        $('#customerId').append(newOption).val(response.data.customer_id);

                        // Close modal dan reset form
                        $('#createCustomer').modal('hide');
                        resetCustomerForm();

                        // Show success message
                        alert('Customer added successfully!');

                        // Trigger customer change untuk update discount
                        $('#customerId').trigger('change');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving customer:', xhr.responseText);

                        // Reset button state
                        submitBtn.html(originalText).prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = 'Please fix the following errors:\n';
                            for (const field in errors) {
                                errorMessage += `• ${errors[field][0]}\n`;
                            }
                            alert(errorMessage);
                        } else if (xhr.status === 500) {
                            alert('Server error. Please try again later.');
                        } else {
                            alert('Error adding customer. Please try again.');
                        }
                    }
                });
            });

            // Function to reset customer form
            function resetCustomerForm() {
                $('#customerForm')[0].reset();
                $('#membershipField').hide();
                $('#is_member').prop('checked', false);
                $('#membership_id').val('');
                $('#membershipInfo').text('');

                // Reset alamat dropdowns
                $('#modal_kota, #modal_kecamatan, #modal_desa').empty().append(
                    '<option value="" hidden selected>Pilih terlebih dahulu</option>'
                );
            }

            // Reset form ketika modal ditutup
            $('#createCustomer').on('hidden.bs.modal', function() {
                resetCustomerForm();
            });

            // Alamat dropdown functionality untuk modal
            $('#modal_provinsi').on('change', function() {
                const provinceId = $(this).val();
                if (provinceId) {
                    loadCities(provinceId, '#modal_kota');
                    $('#modal_kecamatan, #modal_desa').empty().append(
                        '<option value="" hidden selected>Pilih terlebih dahulu</option>'
                    );
                }
            });

            $('#modal_kota').on('change', function() {
                const cityId = $(this).val();
                if (cityId) {
                    loadDistricts(cityId, '#modal_kecamatan');
                    $('#modal_desa').empty().append(
                        '<option value="" hidden selected>Pilih terlebih dahulu</option>'
                    );
                }
            });

            $('#modal_kecamatan').on('change', function() {
                const districtId = $(this).val();
                if (districtId) {
                    loadVillages(districtId, '#modal_desa');
                }
            });

            function loadCities(provinceId, targetSelector) {
                $(targetSelector).empty().append('<option value="">Loading...</option>');

                $.ajax({
                    url: '{{ route('cities') }}',
                    type: 'GET',
                    data: {
                        id: provinceId
                    },
                    success: function(data) {
                        $(targetSelector).empty().append(
                            '<option value="" hidden selected>Pilih Kota</option>');
                        $.each(data, function(index, item) {
                            $(targetSelector).append(
                                '<option value="' + item.id + '">' + item.name + '</option>'
                            );
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading cities:', xhr);
                        $(targetSelector).empty().append(
                            '<option value="">Error loading cities</option>');
                    }
                });
            }

            function loadDistricts(cityId, targetSelector) {
                $(targetSelector).empty().append('<option value="">Loading...</option>');

                $.ajax({
                    url: '{{ route('districts') }}',
                    type: 'GET',
                    data: {
                        id: cityId
                    },
                    success: function(data) {
                        $(targetSelector).empty().append(
                            '<option value="" hidden selected>Pilih Kecamatan</option>');
                        $.each(data, function(index, item) {
                            $(targetSelector).append(
                                '<option value="' + item.id + '">' + item.name + '</option>'
                            );
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading districts:', xhr);
                        $(targetSelector).empty().append(
                            '<option value="">Error loading districts</option>');
                    }
                });
            }

            function loadVillages(districtId, targetSelector) {
                $(targetSelector).empty().append('<option value="">Loading...</option>');

                $.ajax({
                    url: '{{ route('villages') }}',
                    type: 'GET',
                    data: {
                        id: districtId
                    },
                    success: function(data) {
                        $(targetSelector).empty().append(
                            '<option value="" hidden selected>Pilih Desa</option>');
                        $.each(data, function(index, item) {
                            $(targetSelector).append(
                                '<option value="' + item.id + '">' + item.name + '</option>'
                            );
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading villages:', xhr);
                        $(targetSelector).empty().append(
                            '<option value="">Error loading villages</option>');
                    }
                });
            }

            // ==================== TRANSACTION FORM CODE (TETAP SAMA) ====================

            // Customer Change Event - Load membership discount
            $('#customerId').on('change', function() {
                const customerId = $(this).val();
                const selectedOption = $(this).find('option:selected');

                if (customerId) {
                    const discount = selectedOption.data('discount') || 0;
                    discountPercent = parseFloat(discount);

                    if (discountPercent > 0) {
                        $('#discountInfo').removeClass('d-none').text(
                            `Member discount: ${discountPercent}% applied`);
                    } else {
                        $('#discountInfo').addClass('d-none');
                    }

                    calculateTotal();
                } else {
                    discountPercent = 0;
                    $('#discountInfo').addClass('d-none');
                    calculateTotal();
                }
            });

            // Category Change Event
            $('#categoryId').on('change', function() {
                const categoryId = $(this).val();

                if (categoryId) {
                    $('#productId').prop('disabled', false).empty().append(
                        '<option value="" hidden selected>Loading...</option>'
                    );
                    loadProductsByCategory(categoryId);
                    $('#variantId').prop('disabled', true).empty().append(
                        '<option value="" hidden selected>Select Product variant to buy</option>'
                    );
                    $('#stockQty').val('');
                } else {
                    $('#productId, #variantId').prop('disabled', true).empty().append(
                        '<option value="" hidden selected>Select Product Category first</option>'
                    );
                    $('#stockQty').val('');
                }
            });

            // Product Change Event
            $('#productId').on('change', function() {
                const productId = $(this).val();

                if (productId) {
                    $('#variantId').prop('disabled', false).empty().append(
                        '<option value="" hidden selected>Loading...</option>'
                    );
                    loadVariantsByProduct(productId);
                } else {
                    $('#variantId').prop('disabled', true).empty().append(
                        '<option value="" hidden selected>Select Product first</option>'
                    );
                    $('#stockQty').val('');
                }
            });

            // Variant Change Event
            $('#variantId').on('change', function() {
                const selectedVariant = $(this).find('option:selected');
                const stockQty = selectedVariant.data('stock') || 0;
                $('#stockQty').val(stockQty);
                $('#quantity').attr('max', stockQty);
                if (parseInt($('#quantity').val()) > stockQty) {
                    $('#quantity').val(stockQty);
                }
            });

            // Add Item to Table
            $('#addItem').on('click', function() {
                addProductToTable();
            });

            // Payment Calculation
            $('#payment').on('input', calculateChange);
            $('#paymentMethod').on('change', function() {
                if ($(this).val() === 'midtrans') {
                    const totalAfterDiscount = parseFloat($('#totalAfterDiscount').val()) || 0;
                    $('#payment').val(totalAfterDiscount).prop('readonly', true);
                } else {
                    $('#payment').prop('readonly', false).val('');
                }
                calculateChange();
            });

            // Validasi form transaction sebelum submit
            $('form').on('submit', function(e) {
                // Cek jika ini adalah form transaction (bukan customer modal)
                if ($(this).attr('id') !== 'customerForm' && $(this).attr('action') ===
                    '{{ route('transaction.store') }}') {
                    if (productList.length === 0) {
                        e.preventDefault();
                        alert('Please add at least one product to the transaction!');
                        return;
                    }

                    const totalAfterDiscount = parseFloat($('#totalAfterDiscount').val()) || 0;
                    const payment = parseFloat($('#payment').val()) || 0;
                    const paymentMethod = $('#paymentMethod').val();

                    if (paymentMethod === 'cash' && payment < totalAfterDiscount) {
                        e.preventDefault();
                        alert("Payment cannot be less than total amount after discount!");
                        return;
                    }

                    if (paymentMethod === 'midtrans' && payment !== totalAfterDiscount) {
                        e.preventDefault();
                        alert("Payment amount must equal total after discount for Midtrans payments!");
                        return;
                    }
                }
            });

            // AJAX Functions untuk product dan variant
            function loadProductsByCategory(categoryId) {
                $.ajax({
                    url: `/api/categories/${categoryId}/products`,
                    type: 'GET',
                    success: function(response) {
                        $('#productId').empty().append(
                            '<option value="" hidden selected>Select Product to buy</option>'
                        );
                        response.forEach(function(product) {
                            $('#productId').append(
                                `<option value="${product.product_id}">${product.product_name}</option>`
                            );
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading products:', xhr);
                        $('#productId').empty().append(
                            '<option value="" hidden selected>Error loading products</option>'
                        );
                    }
                });
            }

            function loadVariantsByProduct(productId) {
                $.ajax({
                    url: `/api/products/${productId}/variants`,
                    type: 'GET',
                    success: function(response) {
                        $('#variantId').empty().append(
                            '<option value="" hidden selected>Select Product variant to buy</option>'
                        );
                        response.forEach(function(variant) {
                            $('#variantId').append(
                                `<option value="${variant.variant_id}"
                                        data-stock="${variant.stock_qty}"
                                        data-price="${variant.price}">
                                        ${variant.variant_name} - Rp ${variant.price.toLocaleString()}
                                    </option>`
                            );
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading variants:', xhr);
                        $('#variantId').empty().append(
                            '<option value="" hidden selected>Error loading variants</option>'
                        );
                    }
                });
            }

            // Core Functions untuk transaction
            function addProductToTable() {
                const productId = $('#productId').val();
                const productName = $('#productId option:selected').text();
                const variantId = $('#variantId').val();
                const variantName = $('#variantId option:selected').text();
                const price = parseFloat($('#variantId option:selected').data('price'));
                const quantity = parseInt($('#quantity').val());
                const stockQty = parseInt($('#stockQty').val());

                if (!productId || !variantId || !quantity) {
                    alert('Please select product, variant and quantity!');
                    return;
                }

                if (quantity < 1) {
                    alert('Quantity must be at least 1!');
                    return;
                }

                if (quantity > stockQty) {
                    alert(`Insufficient stock! Only ${stockQty} items available.`);
                    return;
                }

                const existingProduct = productList.find(p => p.variantId == variantId);
                if (existingProduct) {
                    const newQty = existingProduct.qty + quantity;
                    if (newQty > stockQty) {
                        alert(
                            `Cannot add more items! Only ${stockQty - existingProduct.qty} additional items available.`);
                        return;
                    }
                    existingProduct.qty = newQty;
                } else {
                    productList.push({
                        productId: productId,
                        productName: productName,
                        variantId: variantId,
                        variantName: variantName,
                        price: price,
                        qty: quantity
                    });
                }

                updateProductTable();
                calculateTotal();
                $('#quantity').val(1);
            }

            function updateProductTable() {
                const tbody = $('#productList tbody');
                tbody.empty();

                productList.forEach((product, index) => {
                    const subtotal = product.price * product.qty;
                    tbody.append(`
                        <tr>
                            <td>${product.productName}</td>
                            <td>${product.variantName}</td>
                            <td>Rp ${product.price.toLocaleString()}</td>
                            <td>
                                <button class="btn btn-sm btn-secondary decrease" data-index="${index}">-</button>
                                ${product.qty}
                                <button class="btn btn-sm btn-secondary increase" data-index="${index}">+</button>
                            </td>
                            <td>Rp ${subtotal.toLocaleString()}</td>
                            <td>
                                <button class="btn btn-danger btn-sm remove" data-index="${index}">Remove</button>
                            </td>
                        </tr>
                    `);
                });

                $('input[name^="items"]').remove();
                productList.forEach((product, index) => {
                    $('form').append(
                        `<input type="hidden" name="items[${index}][product_id]" value="${product.productId}">`
                        );
                    $('form').append(
                        `<input type="hidden" name="items[${index}][variant_id]" value="${product.variantId}">`
                        );
                    $('form').append(
                        `<input type="hidden" name="items[${index}][quantity]" value="${product.qty}">`);
                    $('form').append(
                        `<input type="hidden" name="items[${index}][price]" value="${product.price}">`);
                });
            }

            function calculateTotal() {
                const total = productList.reduce((sum, product) => sum + (product.price * product.qty), 0);
                $('#totalAmount').val(total);
                let discount = 0;
                if (discountPercent > 0) {
                    discount = total * discountPercent / 100;
                }
                $('#discount').val(discount);
                const totalAfterDiscount = total - discount;
                $('#totalAfterDiscount').val(totalAfterDiscount);
                calculateChange();
            }

            function calculateChange() {
                const totalAfterDiscount = parseFloat($('#totalAfterDiscount').val()) || 0;
                const payment = parseFloat($('#payment').val()) || 0;
                const change = payment - totalAfterDiscount;
                $('#change').val(change < 0 ? 0 : change);
            }

            // Event Delegation untuk dynamic elements
            $(document).on('click', '.increase', function() {
                const index = $(this).data('index');
                const product = productList[index];
                const stockQty = parseInt($('#stockQty').val());
                if (product && product.qty < stockQty) {
                    product.qty++;
                    updateProductTable();
                    calculateTotal();
                } else {
                    alert(`Cannot add more items! Maximum ${stockQty} items available.`);
                }
            });

            $(document).on('click', '.decrease', function() {
                const index = $(this).data('index');
                const product = productList[index];
                if (product && product.qty > 1) {
                    product.qty--;
                    updateProductTable();
                    calculateTotal();
                }
            });

            $(document).on('click', '.remove', function() {
                const index = $(this).data('index');
                productList.splice(index, 1);
                updateProductTable();
                calculateTotal();
                $('#payment').val('');
                $('#change').val('');
            });
        });
    </script>
@endsection
