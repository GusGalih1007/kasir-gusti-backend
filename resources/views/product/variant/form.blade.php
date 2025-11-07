@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ $variant ? 'Edit Variant' : 'Create Variant' }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ $variant
                            ? route('product-variant.update', [
                                'variant' => $variant->variant_id,
                                'id' => $variant->product_id,
                            ])
                            : route('product-variant.store', ['id' => $data->product_id]) }}"
                        method="POST" class="needs-validation row g-3" novalidate>
                        {{ csrf_field() }}

                        @if ($variant)
                            @method('PUT')
                        @endif

                        <div class="col-6 form-group">
                            <label for="variantName" class="form-label">Variant Name</label>
                            <input type="text" name="variant_name"
                                value="{{ old('variant_name', $variant->variant_name ?? '') }}" class="form-control"
                                id="variantName" required>
                            <div class="invalid-feedback">
                                variant name is Required
                            </div>
                            @if ($errors->has('variant_name'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('variant_name') }}
                                </div>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label for="variantPrice" class="form-label">Price</label>
                            <div class="input-group mb-3">
                                <input type="number" name="price" value="{{ old('price', $variant->price ?? '') }}"
                                    class="form-control" id="variantPrice" required>
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
                        <div class="col-6 form-group">
                            <label for="variantSku" class="form-label">SKU (Stock Keeping Unit)</label>
                            <div class="input-group mb-3">
                                <input type="text" name="sku" value="{{ old('sku', $variant->sku ?? '') }}"
                                    class="form-control" id="variantSku" required>
                            </div>
                            <div class="invalid-feedback">
                                SKU is Required
                            </div>
                            @if ($errors->has('sku'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('sku') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label for="variantStock" class="form-label">Stock</label>
                            <div class="input-group mb-3">
                                <input type="number" name="stock_qty"
                                    value="{{ old('stock_qty', $variant->stock_qty ?? '') }}" class="form-control"
                                    id="variantStock" required>
                            </div>
                            <div class="invalid-feedback">
                                Stock is Required
                            </div>
                            @if ($errors->has('stock_qty'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('stock_qty') }}
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
