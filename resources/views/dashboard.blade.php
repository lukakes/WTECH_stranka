@extends('layouts.store')

@section('title', 'Account settings')

@section('content')
<div class="profile-page container account-settings-page">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a> &gt; Account</div>

    <section class="profile-card profile-settings-wrap">
        <div class="profile-card-header account-settings-header">
            <div>
                <span class="settings-eyebrow">Signed in as {{ auth()->user()->role }}</span>
                <h1>Account settings</h1>
                <p>Manage your profile, shopping shortcuts, and recent order history from one calm little corner.</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="btn">Edit profile</a>
        </div>

        <div class="settings-grid account-settings-grid">
            <section class="settings-card account-summary-card">
                <h2>Profile</h2>
                <p class="settings-help">Your current account details.</p>

                <div class="profile-card-grid">
                    <div class="profile-item">
                        <span>Name</span>
                        <p>{{ auth()->user()->name }}</p>
                    </div>
                    <div class="profile-item">
                        <span>Email</span>
                        <p>{{ auth()->user()->email }}</p>
                    </div>
                    @if (auth()->user()->isAdmin())
                        <div class="profile-item">
                            <span>Role</span>
                            <p>{{ auth()->user()->role }}</p>
                        </div>
                    @endif
                    <div class="profile-item">
                        <span>Account</span>
                        <p>{{ auth()->user()->email_verified_at ? 'Verified' : 'Not verified yet' }}</p>
                    </div>
                </div>
            </section>

            <section class="settings-card account-summary-card">
                <h2>Quick actions</h2>
                <p class="settings-help">Useful places to jump to while shopping.</p>

                <div class="settings-link-list">
                    <a href="{{ route('products') }}">
                        <span>Browse products</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    <a href="{{ route('cart.index') }}">
                        <span>Open cart</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.products.index') }}">
                            <span>Admin products</span>
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </section>
        </div>
    </section>
</div>
@endsection
