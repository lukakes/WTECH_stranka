@extends('layouts.app')

@section('title', 'Login panel')

@section('content')

<main class="login-page container">
    <div class="login-page-contents">
      <div class="login-page-header">
        <h1>Login</h1>
      </div>
      <div class="login-page-inputs">
        <div class="login-email-field">
          <input type="email" placeholder="Email" />
        </div>
        <div class="login-password-field">
          <input type="password" placeholder="Password" />
          <a href="#" class="forgot-password">Forgot password?</a>
        </div>
        <button class="btn">Log in</button>
      <div class="login-links">
        <a href="Register.html">Create an account</a>
        <a href="index.html">Return to store</a>
      </div>

      <div class="admin-link">
        <a href="AdminLogin.html">Administrator</a>
      </div>
      </div>
    </div>
  </main>

@endsection