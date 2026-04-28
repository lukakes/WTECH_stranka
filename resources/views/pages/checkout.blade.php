@extends('layouts.store')

@section('title', 'Checkout - Sticker Shop')

@section('content')
  <main class="checkout-page container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> &gt; <a href="{{ route('cart.index') }}">Shopping Cart</a> &gt; Checkout
    </div>

    <div class="shopping-cart-header">
      <h1>Checkout</h1>
    </div>

    <div class="checkout-layout">
      <section class="checkout-form-wrap">
        <form class="checkout-form" method="GET" action="{{ route('checkout') }}">
          <h2>Billing details</h2>

          <div class="checkout-grid two-cols">
            <div>
              <label for="first-name">First name</label>
              <input type="text" id="first-name" name="first_name" value="{{ old('first_name') }}" placeholder="Richard" />
            </div>

            <div>
              <label for="last-name">Last name</label>
              <input type="text" id="last-name" name="last_name" value="{{ old('last_name') }}" placeholder="Klein" />
            </div>
          </div>

          <div class="checkout-grid">
            <div>
              <label for="email">Email address</label>
              <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="name@email.com" />
            </div>
          </div>

          <div class="checkout-grid">
            <div>
              <label for="phone">Phone number</label>
              <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+421 900 000 000" />
            </div>
          </div>

          <div class="checkout-grid">
            <div>
              <label for="address">Street address</label>
              <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Main street 12" />
            </div>
          </div>

          <div class="checkout-grid two-cols">
            <div>
              <label for="city">City</label>
              <input type="text" id="city" name="city" value="{{ old('city') }}" placeholder="Nitrianske Pravno" />
            </div>

            <div>
              <label for="postal">Postal code</label>
              <input type="text" id="postal" name="postal" value="{{ old('postal') }}" placeholder="972 13" />
            </div>
          </div>

          <div class="checkout-section">
            <h2>Delivery method</h2>

            <label class="checkout-option">
              <input type="radio" name="delivery" value="courier" checked />
              <span>Courier delivery — €4.90</span>
            </label>

          <div class="checkout-section">
            <h2>Payment method</h2>

            <label class="checkout-option">
              <input type="radio" name="payment" value="card" checked />
              <span>Card payment</span>
            </label>

          <div class="checkout-grid">
            <div>
              <label for="note">Order note</label>
              <textarea id="note" name="note" placeholder="Optional note for your order">{{ old('note') }}</textarea>
            </div>
          </div>

          <div class="checkout-actions">
            <a href="{{ route('cart.index') }}" class="cart-link-back-to-shop">Back to cart</a>
            <button type="submit" class="btn" {{ $cartItems->isEmpty() ? 'disabled' : '' }}>Place order</button>
          </div>
        </form>
      </section>

      <aside class="checkout-summary">
        <h2>Order summary</h2>

        @forelse ($cartItems as $item)
          <div class="checkout-summary-item">
            <span>{{ $item->name }} × {{ $item->quantity }}</span>
            <span>€{{ number_format((float) $item->line_total, 2, ',', '') }}</span>
          </div>
        @empty
          <div class="checkout-summary-item">
            <span>Your cart is empty.</span>
            <span>€0.00</span>
          </div>
        @endforelse

        <div class="checkout-summary-item">
          <span>Delivery</span>
          <span>€{{ number_format((float) $deliveryFee, 2, ',', '') }}</span>
        </div>

        <div class="checkout-summary-total">
          <span>Total</span>
          <span>€{{ number_format((float) $total, 2, ',', '') }}</span>
        </div>
      </aside>
    </div>
  </main>
@endsection
