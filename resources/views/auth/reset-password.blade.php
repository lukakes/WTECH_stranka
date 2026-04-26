@extends('layouts.store')

@section('title', 'Reset Password')
@section('bodyClass', 'login-body')

@section('content')
<main class="login-page container">
    <div class="login-page-contents">
        <div class="login-page-header">
            <h1>Reset password</h1>
        </div>

        <p class="auth-intro">Create a new password for your account.</p>

        <form method="POST" action="{{ route('password.store') }}" class="login-page-inputs auth-stack">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="Email" required autofocus autocomplete="username" />
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input id="password" type="password" name="password" placeholder="New password" required autocomplete="new-password" />
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm new password" required autocomplete="new-password" />
                @error('password_confirmation')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn">Reset password</button>

            <div class="login-links">
                <a href="{{ route('login') }}">Back to login</a>
                <a href="{{ route('home') }}">Return to store</a>
            </div>
        </form>
    </div>
</main>
@endsection
