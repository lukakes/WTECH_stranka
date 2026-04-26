@extends('layouts.store')

@section('title', 'Verify Email')
@section('bodyClass', 'login-body')

@section('content')
<main class="login-page container">
    <div class="login-page-contents">
        <div class="login-page-header">
            <h1>Verify email</h1>
        </div>

        <p class="auth-intro">
            Thanks for signing up. Please verify your email by clicking the link we sent. If you did not receive it, request a new one below.
        </p>

        @if (session('status') === 'verification-link-sent')
            <p class="form-success">A new verification link has been sent to your email address.</p>
        @endif

        <div class="login-page-inputs auth-actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn">Resend verification email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn auth-secondary-btn">Log out</button>
            </form>
        </div>
    </div>
</main>
@endsection
