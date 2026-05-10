@extends('layouts.store')

@section('title', 'Dashboard')

@section('content')
<main class="profile-page container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a> &gt; Dashboard</div>

    <section class="profile-card">
        <div class="profile-card-header">
            <h1>Dashboard</h1>
            <p>You are logged in. Manage your account and explore the store from here.</p>
        </div>

        <div class="profile-actions">
            <a href="{{ route('profile.edit') }}" class="btn">Manage profile</a>
            <a href="{{ route('products') }}" class="btn profile-btn-secondary">Browse products</a>
            <a href="{{ route('cart.index') }}" class="btn profile-btn-secondary">Open cart</a>
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.products.index') }}" class="btn profile-btn-secondary">Admin products</a>
                @endif
            @endauth
        </div>
    </section>

    @if ($orders->isNotEmpty())
        <section class="profile-card">
            <div class="profile-card-header">
                <h2>Your orders</h2>
                <p>Recent orders linked to this account.</p>
            </div>

            <div class="checkout-summary">
                @foreach ($orders as $order)
                    <div class="checkout-summary-item">
                        <span>Order #{{ $order->id }} - {{ $order->stav }}</span>
                        <span>&euro;{{ number_format((float) $order->total, 2, ',', '') }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</main>
@endsection
