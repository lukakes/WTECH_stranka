@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="login-page container">
    <div class="login-page-contents">
        <div class="login-page-header">
            <h1>Create Account</h1>
        </div>

        <div class="login-page-inputs">

            <div>
                <input type="text" placeholder="Full name">
            </div>

            <div>
                <input type="email" placeholder="Email">
            </div>

            <div>
                <input type="password" placeholder="Password">
            </div>

            <div>
                <input type="password" placeholder="Confirm password">
            </div>

            <button class="btn">Create account</button>

            <div class="login-links">
                <a href="#">Already have an account?</a>
                <a href="{{ route('home') }}">Return to store</a>
            </div>

        </div>
    </div>
</div>
@endsection