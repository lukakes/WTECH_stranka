@extends('layouts.store')

@section('title', 'Login panel')
@section('bodyClass', 'login-body')

@section('content')

<main class="login-page container">
    <div class="login-page-contents">
      <div class="login-page-header">
        <h1>Login</h1>
      </div>

      @if (session('status'))
        <p class="form-success">{{ session('status') }}</p>
      @endif

      <form method="POST" action="{{ route('login') }}" class="login-page-inputs">
        @csrf

        <div class="login-email-field">
          <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus autocomplete="username" />
          @error('email')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="login-password-field">
          <input id="password" type="password" name="password" placeholder="Password" required autocomplete="current-password" />
          @error('password')
            <p class="form-error">{{ $message }}</p>
          @enderror

          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
          @endif
        </div>

        <button type="submit" class="btn">Log in</button>
        <div class="login-links">
          <a href="{{ route('register') }}">Create an account</a>
          <a href="{{ route('home') }}">Return to store</a>
        </div>

        <div class="admin-link">
          <a href="{{ route('dashboard') }}">Dashboard</a>
        </div>
      </form>
    </div>
  </main>

@endsection