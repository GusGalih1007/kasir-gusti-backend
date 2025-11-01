@extends('layout.app')
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
                            <label for="membershipBenefit" class="form-label">Benefit</label>
                            <input type="text" name="benefit" value="{{ old('benefit', $membership->benefit ?? '') }}"
                                class="form-control" id="membershipLevel" required>
                            <div class="invalid-feedback">
                                Benefit is Required
                            </div>
                            @if ($errors->has('benefit'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('benefit') }}
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
                        <div class="col-6 form-group">
                            <label for="membershipLevel" class="form-label">Subcription Length (Month)</label>
                            <div class="input-group mb-3">
                                <input type="number" name="expiration_period"
                                    value="{{ old('membership', $membership->expiration_period ?? '') }}"
                                    class="form-control" id="membershipLevel" aria-describedby="basic-addon2" required>
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
