@extends('admin.layouts.auth')
@section('title', 'Register')

@section('content')
<p class="login-box-msg">Register a new account</p>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="input-group mb-3">
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Full name">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-user"></span>
            </div>
        </div>
    </div>
    @error('name')
        <div class="text-danger mb-2">{{ $message }}</div>
    @enderror

    <div class="input-group mb-3">
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Email">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
    </div>
    @error('email')
        <div class="text-danger mb-2">{{ $message }}</div>
    @enderror

    <div class="input-group mb-3">
        <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password" placeholder="Password">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>
    @error('password')
        <div class="text-danger mb-2">{{ $message }}</div>
    @enderror

    <div class="input-group mb-3">
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm password">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>
    @error('password_confirmation')
        <div class="text-danger mb-2">{{ $message }}</div>
    @enderror

    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </div>
    </div>

    <p class="mb-0 mt-3">
        <a href="{{ route('login') }}" class="text-center">I already have an account</a>
    </p>
</form>
@endsection
