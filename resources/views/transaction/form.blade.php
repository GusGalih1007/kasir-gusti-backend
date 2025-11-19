@extends('layout.app')
@section('title', 'Transaction Form')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Transaction Form</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group col-21">
                    <label for="customerId">Customer</label>
                    <select name="customer_id" id="customerId">
                        <option value="" hidden selected>Select Customer</option>
                        @foreach ($customer as $item)
                            <option value="{{ $item->customer_id }}"></option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection