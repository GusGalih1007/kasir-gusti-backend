@extends('layout.app')
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
                    <form action="{{ $customer ? route('customer.update', $customer->customer_id) :  route('customer.store') }}" method="POST" class="needs-validation row g-3" novalidate>
                        {{ csrf_field() }}

                        @if ($customer)
                            @method('PUT')
                        @endif

                        <div class="col-6 form-group">
                            <label for="customerFirstName" class="form-label">Firstname</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name ?? '') }}" class="form-control" id="customerFirstName" required>
                            <div class="invalid-feedback">
                                    Firstname is Required
                            </div>
                            @if ($errors->has('first_name'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('first_name') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label for="customerLastName" class="form-label">Lastname</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name ?? '') }}" class="form-control" id="customerLastName" required>
                            <div class="invalid-feedback">
                                    Lastname is Required
                            </div>
                            @if ($errors->has('last_name'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('last_name') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label for="customerPhone" class="form-label">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="form-control" id="customerPhone" required>
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
                            <label for="customerLastName" class="form-label">Lastname</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name ?? '') }}" class="form-control" id="customerLastName" required>
                            <div class="invalid-feedback">
                                    Email is Required
                            </div>
                            @if ($errors->has('last_name'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('last_name') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label" for="alamat">Alamat</label>
                            <textarea class="form-control" name="alamat" id="alamat" rows="5" required>{{ $customer ? $customer->alamat : '' }}</textarea>
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
