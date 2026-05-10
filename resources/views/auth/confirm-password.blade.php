@extends('layouts.store')

@section('title', 'Confirm Password')
@section('bodyClass', 'login-body')

@section('content')
<main class="login-page container">
    <div class="login-page-contents">
        <div class="login-page-header">
            <h1>Confirm password</h1>
        </div>

        <p class="auth-intro">For security reasons, please enter your password to continue.</p>

        <form method="POST" action="{{ route('password.confirm') }}" class="login-page-inputs auth-stack">
            @csrf

            <div>
                <input id="password" type="password" name="password" placeholder="Password" required autocomplete="current-password" />
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn">Confirm</button>

            <div class="login-links">
                <a href="{{ route('profile.edit') }}">Back to profile</a>
                <a href="{{ route('home') }}">Return to store</a>
            </div>
        </form>
    </div>
</main>
@endsection
