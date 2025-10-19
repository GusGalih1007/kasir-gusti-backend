@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Categories</h4>
                    </div>
                    <div style="text-align: right;">
                        <a href="{{ route('category.create') }}" class="btn btn-primary">
                            Create
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- <p>Images in Bootstrap are made responsive with <code>.img-fluid</code>. <code>max-width: 100%;</code>
                        and <code>height: auto;</code> are applied to the image so that it scales with the parent element.
                    </p> --}}
                    <div class="custom-datatable-entries table-responsive mt-4">
                        <table id="datatable" class="table table-striped table-hover" data-toggle="data-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Parent Category</th>
                                </tr>
                            </thead>
                            <tbody class="table-light">
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->parentId->name ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">Total Data</th>
                                    <th>{{ count($data) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
