    @extends('layout.app')
    @section('title', 'Customer Form')
    @section('content')
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $customer ? 'Edit Customer' : 'Create Customer' }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ $customer ? route('customer.update', $customer->customer_id) : route('customer.store') }}"
                            method="POST" class="needs-validation row g-3" novalidate>
                            {{ csrf_field() }}

                            @if ($customer)
                                @method('PUT')
                            @endif

                            <div class="col-4 form-group">
                                <label for="customerFirstName" class="form-label">Firstname</label>
                                <input type="text" name="first_name"
                                    value="{{ old('first_name', $customer->first_name ?? '') }}" class="form-control"
                                    id="customerFirstName" required>
                                <div class="invalid-feedback">
                                    Firstname is Required
                                </div>
                                @if ($errors->has('first_name'))
                                    <span class="alert alert-danger">
                                        {{ $errors->first('first_name') }}
                                    </span>
                                @endif
                            </div>
                            <div class="col-4 form-group">
                                <label for="customerLastName" class="form-label">Lastname</label>
                                <input type="text" name="last_name"
                                    value="{{ old('last_name', $customer->last_name ?? '') }}" class="form-control"
                                    id="customerLastName" required>
                                <div class="invalid-feedback">
                                    Lastname is Required
                                </div>
                                @if ($errors->has('last_name'))
                                    <span class="alert alert-danger">
                                        {{ $errors->first('last_name') }}
                                    </span>
                                @endif
                            </div>
                            <div class="col-4 form-group">
                                <label for="customerPhone" class="form-label">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                                    class="form-control" id="customerPhone" required>
                                <div class="invalid-feedback">
                                    Phone is Required
                                </div>
                                @if ($errors->has('phone'))
                                    <span class="alert alert-danger">
                                        {{ $errors->first('phone') }}
                                    </span>
                                @endif
                            </div>
                            <div class="col-6 form-group">
                                <label for="customerLastName" class="form-label">E-Mail</label>
                                <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
                                    class="form-control" id="customerLastName" required>
                                <div class="invalid-feedback">
                                    Email is Required
                                </div>
                                @if ($errors->has('email'))
                                    <span class="alert alert-danger">
                                        {{ $errors->first('email') }}
                                    </span>
                                @endif
                            </div>
                            <div class="col-6 form-group">
                                <label class="form-label" for="membership">Membership</label>
                                <select name="membership" class="form-select" data-trigger id="membership"
                                    aria-placeholder="Select customer membership">
                                    <option value="" selected hidden>Select customer membership</option>
                                    @foreach ($membership as $item)
                                        <option value="{{ $item->membership_id }}"
                                            {{ $customer ? ($item->membership_id == $customer->membership_id ? 'selected' : '') : '' }}>
                                            {{ $item->membership }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="provinsi" class="form-label">Provinsi</label>
                                <select name="provinsi" id="provinsi" class="form-select">
                                    <option value="" hidden selected>==Pilih Salah Satu==</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="kota" class="form-label">Kabupaten(Kota)</label>
                                <select name="kota" id="kota" class="form-select">
                                    <option value="" hidden selected>==Pilih Salah Satu==</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="kecamatan" class="form-label">Kecamatan</label>
                                <select name="kecamatan" id="kecamatan" class="form-select">
                                    <option value="" hidden selected>==Pilih Salah Satu==</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="desa" class="form-label">Desa(Kelurahan)</label>
                                <select name="desa" id="desa" class="form-select">
                                    <option value="" hidden selected>==Pilih Salah Satu==</option>
                                </select>
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label" for="alamat">Alamat</label>
                                <textarea class="form-control" name="alamat" id="alamat" rows="5" required>{{ $customer ? $customer->alamat : old('alamat', $customer->alamat ?? '') }}</textarea>
                                <div class="invalid-feedback">
                                    Alamat is required
                                </div>
                                @if ($errors->has('alamat'))
                                    <span class="alert alert-danger">
                                        {{ $errors->first('alamat') }}
                                    </span>
                                @endif
                            </div>
                            <div class="col-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('customer.index') }}" class="btn btn-light">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script>
            function onChangeSelect(url, id, name) {
                // Clear previous options and show loading
                $('#' + name).empty().append('<option>Loading...</option>');

                // Send ajax request to get the data
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        $('#' + name).empty();
                        $('#' + name).append('<option value="">==Pilih Salah Satu==</option>');

                        // Loop through the array of objects
                        $.each(data, function(index, item) {
                            $('#' + name).append('<option value="' + item.id + '">' + item.name +
                                '</option>');
                        });
                    },
                    error: function() {
                        $('#' + name).empty();
                        $('#' + name).append('<option value="">Error loading data</option>');
                    }
                });
            }

            $(function() {
                $('#provinsi').on('change', function() {
                    const provinceId = $(this).val();
                    if (provinceId) {
                        onChangeSelect('{{ route('cities') }}', provinceId, 'kota');
                        // Reset dependent dropdowns
                        $('#kecamatan').empty().append('<option value="">==Pilih Salah Satu==</option>');
                        $('#desa').empty().append('<option value="">==Pilih Salah Satu==</option>');
                    } else {
                        $('#kota').empty().append('<option value="">==Pilih Salah Satu==</option>');
                        $('#kecamatan').empty().append('<option value="">==Pilih Salah Satu==</option>');
                        $('#desa').empty().append('<option value="">==Pilih Salah Satu==</option>');
                    }
                });

                $('#kota').on('change', function() {
                    const cityId = $(this).val();
                    if (cityId) {
                        onChangeSelect('{{ route('districts') }}', cityId, 'kecamatan');
                        // Reset dependent dropdown
                        $('#desa').empty().append('<option value="">==Pilih Salah Satu==</option>');
                    } else {
                        $('#kecamatan').empty().append('<option value="">==Pilih Salah Satu==</option>');
                        $('#desa').empty().append('<option value="">==Pilih Salah Satu==</option>');
                    }
                });

                $('#kecamatan').on('change', function() {
                    const districtId = $(this).val();
                    if (districtId) {
                        onChangeSelect('{{ route('villages') }}', districtId, 'desa');
                    } else {
                        $('#desa').empty().append('<option value="">==Pilih Salah Satu==</option>');
                    }
                });
            });
        </script>
    @endsection
