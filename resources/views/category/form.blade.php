@extends('layout.app')
@section('title', 'Product Category Form')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ $category ? 'Edit Category' : 'Create Category' }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ $category ? route('category.update', $category->category_id) : route('category.store') }}"
                        method="POST" class="needs-validation row g-3" novalidate>
                        {{ csrf_field() }}

                        @if ($category)
                            @method('PUT')
                        @endif

                        <div class="col-6 form-group">
                            <label for="CategoryName" class="form-label">Category Name</label>
                            <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
                                class="form-control" id="CategoryName" required>
                            <div class="invalid-feedback">
                                Category name is Required
                            </div>
                            @if ($errors->has('name'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('name') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label for="parentId" class="form-label">Parent Category</label>
                            <select name="parent_id" id="parentId" class="form-control select2">
                                <option value="" selected hidden>Select Category</option>
                                @foreach ($parent as $p)
                                    <option value="{{ $p->category_id }}"
                                        {{ $category ? ($p->category_id == $category->parent_id ? 'selected' : '') : '' }}>
                                        {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="5" required>{{ $category ? $category->description : '' }}</textarea>
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
                        <a href="{{ route('category.index') }}" class="btn btn-light">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var jq = jQuery.noConflict(true);
        jq(document).ready(function() {
            jq('.select2').select2();
        });
    </script>
@endsection
