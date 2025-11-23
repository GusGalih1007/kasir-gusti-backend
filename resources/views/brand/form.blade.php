@extends('layout.app')
@section('title', 'Product Brand Form')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ $brand ? 'Edit Brand' : 'Create Brand' }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ $brand ? route('brand.update', $brand->brand_id) : route('brand.store') }}" method="POST" class="needs-validation row g-3" novalidate>
                        {{ csrf_field() }}

                        @if ($brand)
                            @method('PUT')
                        @endif

                        <div class="col-12 form-group">
                            <label for="BrandName" class="form-label">Brand Name</label>
                            <input type="text" name="name" value="{{ old('name', $brand->name ?? '') }}" class="form-control" id="BrandName" required>
                            <div class="invalid-feedback">
                                    Brand name is Required
                            </div>
                            @if ($errors->has('name'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('name') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="5" required>{{ $brand ? $brand->description : '' }}</textarea>
                            <div class="invalid-feedback">
                                Description is required
                            </div>
                            {{-- @if ($errors->has('description'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('description') }}
                                </span>
                            @endif --}}
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('brand.index') }}" class="btn btn-light">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
