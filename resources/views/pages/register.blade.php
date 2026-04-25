@extends('layouts.store')

@section('title', 'Register')
@section('bodyClass', 'login-body')

@section('content')
<main class="login-page container">
    <div class="login-page-contents">
        <div class="login-page-header">
            <h1>Create Account</h1>
        </div>

        <form method="POST" action="{{ route('register') }}" class="login-page-inputs">
            @csrf

            <div>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Full name" required autofocus autocomplete="name">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="username">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input id="password" type="password" name="password" placeholder="Password" required autocomplete="new-password">
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm password" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn">Create account</button>

            <div class="login-links">
                <a href="{{ route('login') }}">Already have an account?</a>
                <a href="{{ route('home') }}">Return to store</a>
            </div>

        </form>
    </div>
</main>
@endsection