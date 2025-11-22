@extends('layout.app')
@section('title', 'Customer Membership Form')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ $membership ? 'Edit Membership' : 'Create Membership' }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ $membership ? route('membership.update', $membership->membership_id) : route('membership.store') }}"
                        method="POST" class="needs-validation row g-3" novalidate>
                        {{ csrf_field() }}

                        @if ($membership)
                            @method('PUT')
                        @endif

                        <div class="col-6 form-group">
                            <label for="membershipLevel" class="form-label">Membership Level</label>
                            <input type="text" name="membership"
                                value="{{ old('membership', $membership->membership ?? '') }}" class="form-control"
                                id="membershipLevel" required>
                            <div class="invalid-feedback">
                                Membership Level is Required
                            </div>
                            @if ($errors->has('membership'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('membership') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label for="membershipDiscount" class="form-label">Discount</label>
                            <div class="input-group mb-3">
                                <input type="number" name="discount" value="{{ old('discount', $membership->discount ?? '') }}"
                                    class="form-control" id="membershipDiscount" aria-describedby="discountPercent" required>
                                    <span class="input-group-text" id="discountPercent">%</span>
                            </div>
                            <div class="invalid-feedback">
                                Discount is Required
                            </div>
                            @if ($errors->has('discount'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('discount') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-12 form-group">
                            <label for="membershipBenefit" class="form-label">Benefit</label>
                            <textarea type="text" name="benefit"
                                class="form-control" rows="5" id="membershipLevel" required>{{ old('benefit', $membership->benefit ?? '') }}</textarea>
                            <div class="invalid-feedback">
                                Benefit is Required
                            </div>
                            @if ($errors->has('benefit'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('benefit') }}
                                </span>
                            @endif
                        </div>
                        {{-- <div class="col-6 form-group">
                            <label for="expiration_period" class="form-label">Subcription Length (Month)</label>
                            <div class="input-group mb-3">
                                <input type="number" name="expiration_period"
                                    value="{{ old('expiration_period', $membership->expiration_period ?? '') }}"
                                    class="form-control" id="expiration_period" aria-describedby="basic-addon2">
                                <span class="input-group-text" id="basic-addon2">Month</span>
                            </div>
                            <div class="invalid-feedback">
                                Subcription Length is Required
                            </div>
                            @if ($errors->has('expiration_period'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('expiration_period') }}
                                </span>
                            @endif
                        </div> --}}
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('membership.index') }}" class="btn btn-light">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
