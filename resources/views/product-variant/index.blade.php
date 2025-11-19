@extends('layout.app')
@section('title', "{$productParent->product_name} Variant")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">
                            {{ $productParent->product_name . ' Variant' }}
                        </h4>
                    </div>
                    <div style="text-align: right;">
                        <a href="{{ route('product-variant.create', $productParent->slug) }}" class="btn btn-primary">
                            Create
                        </a>
                        {{-- <button class="btn btn-primary" data-bs-toggle="createModal"
                            data-bs-target="createVariant{{ $productParent->product_id }}">
                        </button> --}}
                    </div>
                </div>
                <div class="card-body">
                    {{-- <p>Images in Bootstrap are made responsive with <code>.img-fluid</code>.
                        <code>max-width: 100%;</code>
                        and <code>height: auto;</code> are applied to the image so that it scales with the parent element.
                    </p> --}}
                    <div class="custom-datatable-entries table-responsive">
                        <table id="datatable" data-toggle="data-table" class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Variant Name</th>
                                    <th>Variant Price</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->variant_name }}</td>
                                        <td>{{ 'Rp. ' . number_format($item->price, 2, ',', '.') }}</td>
                                        <td>{{ $item->sku }}</td>
                                        <td>{{ $item->stock_qty }}</td>
                                        <td>
                                            <a href="{{ route('product-variant.edit', [
                                                'id' => $item->variant_id,
                                                'product' => $item->product->slug,
                                            ]) }}"
                                                class="btn btn-warning btn-sm"> <svg class="icon-16" width="16"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M15.1655 4.60254L19.7315 9.16854" stroke="currentColor"
                                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                </svg> </a>
                                            <button type="button" data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirm{{ $item->variant_id }}"
                                                class="btn btn-sm btn-danger"> <svg class="icon-16" width="16"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path
                                                        d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                </svg> </button>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="deleteConfirm{{ $item->variant_id }}"
                                        data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                        aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Warning</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Do you want to delete this data?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">No</button>
                                                    <form
                                                        action="{{ route('product-variant.destroy', [
                                                            'id' => $item->variant_id,
                                                            'product' => $item->product->slug,
                                                        ]) }}"
                                                        method="POST">
                                                        {{ csrf_field() }}
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Yes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- <div class="createModal fade" id="createVariant" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="createVariantLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $variant ? 'Edit Variant' : 'Create Variant' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ $variant
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
@endsection
