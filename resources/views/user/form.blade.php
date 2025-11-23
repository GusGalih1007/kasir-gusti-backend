@extends('layout.app')
@section('title', 'User Form')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ $user ? 'Edit User' : 'Create User' }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ $user ? route('user.update', $user->user_id) : route('user.store') }}"
                        method="POST" class="needs-validation row g-3" novalidate>
                        {{ csrf_field() }}

                        @if ($user)
                            @method('PUT')
                        @endif

                        <div class="col-4 form-group">
                            <label for="userFirstName" class="form-label">Firstname</label>
                            <input type="text" name="first_name"
                                value="{{ old('first_name', $user->first_name ?? '') }}" class="form-control"
                                id="userFirstName" required>
                            <div class="invalid-feedback">
                                Firstname is Required
                            </div>
                            @if ($errors->has('first_name'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('first_name') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-4 form-group">
                            <label for="userLastName" class="form-label">Lastname</label>
                            <input type="text" name="last_name"
                                value="{{ old('last_name', $user->last_name ?? '') }}" class="form-control"
                                id="userLastName" required>
                            <div class="invalid-feedback">
                                Lastname is Required
                            </div>
                            @if ($errors->has('last_name'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('last_name') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-4 form-group">
                            <label for="userUsername" class="form-label">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username ?? '') }}"
                                class="form-control" id="userUsername" required>
                            <div class="invalid-feedback">
                                Username is Required
                            </div>
                            @if ($errors->has('username'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('username') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label for="userPhone" class="form-label">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                                class="form-control" id="userPhone" required>
                            <div class="invalid-feedback">
                                Phone is Required
                            </div>
                            @if ($errors->has('phone'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('phone') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label for="userLastName" class="form-label">E-Mail</label>
                            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                                class="form-control" id="userLastName" required>
                            <div class="invalid-feedback">
                                Email is Required
                            </div>
                            @if ($errors->has('email'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('email') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label" for="membership">Role</label>
                            <select name="role" class="form-select" data-trigger id="role"
                                aria-placeholder="Select user membership" required>
                                <option value="" selected hidden>Select Role</option>
                                @foreach ($role as $item)
                                    <option value="{{ $item->role_id }}"
                                        {{ $user ? ($item->role_id == $user->role_id ? 'selected' : '') : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Role is required
                            </div>
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label" for="membership">Status</label>
                            <select name="status" class="form-select" data-trigger id="status"
                                aria-placeholder="Select user role">
                                <option value="Active" selected>Active</option>
                                <option value="InActive" >InActive</option>
                            </select>
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label" for="uaerPassword">Password</label>
                            <input type="password" class="form-control" name="password" id="userPassword"
                            {{ $user ? '' : 'required' }}>
                            <div class="invalid-feedback">
                                Password is required
                            </div>
                            @if ($errors->has('password'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label" for="passwordConfirm">Password Confirm</label>
                            <input type="password" class="form-control" name="password_confirmation" id="passwordConfirm"
                            {{ $user ? '' : 'required' }}>
                            <div class="invalid-feedback">
                                Please confirm your password
                            </div>
                            @if ($errors->has('password_confirmation'))
                                <span class="alert alert-danger">
                                    {{ $errors->first('password_confirmation') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('user.index') }}" class="btn btn-light">Back</a>
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
