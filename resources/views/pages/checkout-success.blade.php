@extends('layouts.store')

@section('title', 'Order confirmation - Sticker Shop')

@section('content')
  <main class="checkout-page container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> &gt; Order confirmation
    </div>

    <div class="shopping-cart-header">
      <h1>Order confirmed</h1>
    </div>

    @if (session('checkout_success'))
      <p class="form-success">{{ session('checkout_success') }}</p>
    @endif

    <div class="checkout-layout">
      <section class="checkout-form-wrap">
        <div class="checkout-form">
          <h2>Thank you, {{ $order->zakaznik?->meno ?? 'customer' }}</h2>
          <p>Your order #{{ $order->id }} has been saved and is waiting for processing.</p>

          @guest
            @if ($isClaimableOrder)
              <p>
                Create an account or log in now to save this order to your account history.
              </p>
              <div class="checkout-actions">
                <a href="{{ route('register') }}" class="btn">Register and save order</a>
                <a href="{{ route('login') }}" class="cart-link-back-to-shop">Log in</a>
              </div>
            @endif
          @else
            @if ($order->user_id === auth()->id())
              <p>This order is saved to your account.</p>
            @endif
          @endguest

          <div class="checkout-actions">
            <a href="{{ route('products') }}" class="btn">Continue shopping</a>
          </div>
        </div>
      </section>

      <aside class="checkout-summary">
        <h2>Order summary</h2>

        @foreach ($order->polozky as $item)
          <div class="checkout-summary-item">
            <span>{{ $item->variant?->product?->nazov ?? 'Product' }} &times; {{ $item->mnozstvo }}</span>
            <span>&euro;{{ number_format((float) $item->celkova_cena, 2, ',', '') }}</span>
          </div>
        @endforeach

        <div class="checkout-summary-item">
          <span>Delivery</span>
          <span>&euro;{{ number_format((float) $order->doprava_cena, 2, ',', '') }}</span>
        </div>

        @if ((float) $order->platba_poplatok > 0)
          <div class="checkout-summary-item">
            <span>Payment fee</span>
            <span>&euro;{{ number_format((float) $order->platba_poplatok, 2, ',', '') }}</span>
          </div>
        @endif

        <div class="checkout-summary-total">
          <span>Total</span>
          <span>&euro;{{ number_format((float) $order->total, 2, ',', '') }}</span>
        </div>
      </aside>
    </div>
  </main>
@endsection
