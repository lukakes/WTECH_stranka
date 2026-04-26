@extends('layouts.store')

@section('title', 'Profile')

@section('content')
<main class="profile-page container profile-settings-page">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a> &gt; Profile</div>

    <section class="profile-card profile-settings-wrap">
        <div class="profile-card-header">
            <h1>Account settings</h1>
            <p>Update your profile details, password, and account preferences.</p>
        </div>

        <div class="settings-grid">
            <section class="settings-card">
                <h2>Profile information</h2>
                <p class="settings-help">Update your account name and email address.</p>

                @if (session('status') === 'profile-updated')
                    <p class="form-success">Profile updated successfully.</p>
                @endif

                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                </form>

                <form method="POST" action="{{ route('profile.update') }}" class="settings-form">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <p class="settings-help">
                                Your email address is unverified.
                                <button type="submit" form="send-verification" class="inline-link">Click here to resend verification email.</button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="form-success">A new verification link has been sent to your email address.</p>
                            @endif
                        @endif
                    </div>

                    <div class="settings-actions">
                        <button type="submit" class="btn">Save profile</button>
                    </div>
                </form>
            </section>

            <section class="settings-card">
                <h2>Update password</h2>
                <p class="settings-help">Use a long, random password to keep your account secure.</p>

                @if (session('status') === 'password-updated')
                    <p class="form-success">Password updated successfully.</p>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="settings-form">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password">Current password</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password">New password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password">
                        @error('password', 'updatePassword')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                        @error('password_confirmation', 'updatePassword')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="settings-actions">
                        <button type="submit" class="btn">Save password</button>
                    </div>
                </form>
            </section>

            <section class="settings-card danger-card">
                <h2>Delete account</h2>
                <p class="settings-help">
                    Once deleted, your account and related resources are permanently removed. Enter your password to confirm.
                </p>

                <form method="POST" action="{{ route('profile.destroy') }}" class="settings-form" onsubmit="return confirm('Are you sure you want to permanently delete your account?');">
                    @csrf
                    @method('delete')

                    <div>
                        <label for="delete_password">Password</label>
                        <input id="delete_password" name="password" type="password" autocomplete="current-password" placeholder="Enter password to confirm deletion">
                        @error('password', 'userDeletion')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="settings-actions">
                        <button type="submit" class="btn danger-btn">Delete account</button>
                    </div>
                </form>
            </section>
        </div>
    </section>
</main>
@endsection
