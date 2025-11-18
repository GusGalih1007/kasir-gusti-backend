@extends('layout.app')
@section('title', 'Product Supplier Form')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ $supplier ? 'Edit Supplier' : 'Create Supplier' }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ $supplier ? route('supplier.update', $supplier->supplier_id) :  route('supplier.store') }}" method="POST" class="needs-validation row g-3" novalidate>
                        {{ csrf_field() }}

                        @if ($supplier)
                            @method('PUT')
                        @endif

                        <div class="col-12 form-group">
                            <label for="SupplierName" class="form-label">Supplier Name</label>
                            <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" class="form-control" id="SupplierName" required>
                            <div class="invalid-feedback">
                                    Supplier name is Required
                            </div>
                            @if ($errors->has('name'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('name') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="5" required>{{ $supplier ? $supplier->description : '' }}</textarea>
                            <div class="invalid-feedback">
                                Description is required
                            </div>
                            {{-- @if ($errors->has('description'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('description') }}
                                </span>
                            @endif --}}
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label" for="alamat">Alamat</label>
                            <textarea class="form-control" name="alamat" id="alamat" rows="5" required>{{ $supplier ? $supplier->alamat : '' }}</textarea>
                            <div class="invalid-feedback">
                                Alamat is required
                            </div>
                            {{-- @if ($errors->has('description'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('description') }}
                                </span>
                            @endif --}}
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
