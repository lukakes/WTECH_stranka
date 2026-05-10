@extends('layouts.store')

@section('title', 'Forgot Password')
@section('bodyClass', 'login-body')

@section('content')
<main class="login-page container">
    <div class="login-page-contents">
        <div class="login-page-header">
            <h1>Forgot password</h1>
        </div>

        <p class="auth-intro">
            Enter the email used for your account and we will send you a link to reset your password.
        </p>

        @if (session('status'))
            <p class="form-success">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="login-page-inputs auth-stack">
            @csrf

            <div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus autocomplete="username" />
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn">Send reset link</button>

            <div class="login-links">
                <a href="{{ route('login') }}">Back to login</a>
                <a href="{{ route('home') }}">Return to store</a>
            </div>
        </form>
    </div>
</main>
@endsection
