@extends('admin.layouts.auth')
@section('title', 'Login')

@section('content')
<p class="login-box-msg">Sign in to start your session</p>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="input-group mb-3">
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Email">
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
        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" placeholder="Password">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>
    @error('password')
        <div class="text-danger mb-2">{{ $message }}</div>
    @enderror

    <div class="row">
        <div class="col-8">
            <div class="icheck-primary">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember Me</label>
            </div>
        </div>

        <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </div>
    </div>

    @if (Route::has('password.request'))
        <p class="mb-1 mt-3">
            <a href="{{ route('password.request') }}">I forgot my password</a>
        </p>
    @endif

    <p class="mb-0">
        {{-- <a href="{{ route('register') }}" class="text-center">Register a new account</a> --}}
    </p>
</form>
@endsection
