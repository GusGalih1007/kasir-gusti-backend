@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Custom Validation</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('category.store') }}" method="POST" class="needs-validation row g-3" novalidate>
                        {{ csrf_field() }}
                        <div class="col-6 form-group">
                            <label for="CategoryName" class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control" id="CategoryName" required>
                            <div class="invalid-feedback">
                                    Category name is Required
                            </div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="parentCategory" class="form-label">Parent Category</label>
                            <select name="name" class="form-control" id="parentCategory">
                                
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="5" required></textarea>
                            <div class="invalid-feedback">
                                Description is required
                            </div>
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
