@extends('layout.app')
@section('title', 'Product Form')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ $product ? 'Edit Product' : 'Create Product' }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ $product ? route('product.update', $product->product_id) : route('product.store') }}"
                        method="POST" class="needs-validation row g-3" enctype="multipart/form-data" novalidate>
                        {{ csrf_field() }}

                        @if ($product)
                            @method('PUT')
                        @endif

                        <div class="col-6 form-group">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" name="product_name"
                                value="{{ old('product_name', $product->product_name ?? '') }}" class="form-control"
                                id="productName" required>
                            <div class="invalid-feedback">
                                Product name is Required
                            </div>
                            @if ($errors->has('product_name'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('product_name') }}
                                </div>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label for="productPrice" class="form-label">Price</label>
                            <div class="input-group mb-3">
                                <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}"
                                    class="form-control" id="productPrice" required>
                                <span class="input-group-text">.00</span>
                            </div>
                            <div class="invalid-feedback">
                                Price is Required
                            </div>
                            @if ($errors->has('price'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('price') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-4 form-group">
                            <label class="form-label" for="brand">Brand</label>
                            <select name="brand" class="form-select" data-trigger id="brand"
                                aria-placeholder="Select customer brand">
                                <option value="" selected hidden>Select brand</option>
                                @foreach ($brand as $item)
                                    <option value="{{ $item->brand_id }}"
                                        {{ $product ? ($item->brand_id == $product->brand_id ? 'selected' : '') : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('brand'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('brand') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-4 form-group">
                            <label class="form-label" for="category">Category</label>
                            <select name="category" class="form-select" data-trigger id="category">
                                <option value="" selected hidden>Select category</option>
                                @foreach ($category as $item)
                                    <option value="{{ $item->category_id }}"
                                        {{ $product ? ($item->category_id == $product->category_id ? 'selected' : '') : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('category'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('category') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-4 form-group">
                            <label class="form-label" for="supplier">Supplier</label>
                            <select name="supplier" class="form-select" data-trigger id="supplier"
                                aria-placeholder="Select customer supplier">
                                <option value="" selected hidden>Select supplier</option>
                                @foreach ($supplier as $item)
                                    <option value="{{ $item->supplier_id }}"
                                        {{ $product ? ($item->supplier_id == $product->supplier_id ? 'selected' : '') : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('supplier'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('supplier') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-12 form-group">
                            <div class="form-group">
                                <label for="photo" class="form-label custom-file-input">Product Photo</label>
                                <input type="file" name="photo" class="form-control" id="photo">
                            </div>
                            @if ($errors->has('photo'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('photo') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="5" required>{{ $product ? $product->description : '' }}</textarea>
                            <div class="invalid-feedback">
                                Description is required
                            </div>
                            @if ($errors->has('description'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('description') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('product.index') }}" class="btn btn-light">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
