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

    @if ($errors->any())
      <p class="form-error">Please check the highlighted fields and try again.</p>
    @endif

    <div class="checkout-layout">
      <section class="checkout-form-wrap">
        <form class="checkout-form" method="POST" action="{{ route('checkout.store') }}">
          @csrf

          <h2>Billing details</h2>

          <div class="checkout-grid two-cols">
            <div>
              <label for="first-name">First name</label>
              <input type="text" id="first-name" name="first_name" value="{{ old('first_name') }}" placeholder="Richard" required />
              @error('first_name')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="last-name">Last name</label>
              <input type="text" id="last-name" name="last_name" value="{{ old('last_name') }}" placeholder="Klein" required />
              @error('last_name')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="checkout-grid">
            <div>
              <label for="email">Email address</label>
              <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="name@email.com" required />
              @error('email')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="checkout-grid">
            <div>
              <label for="phone">Phone number</label>
              <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+421 900 000 000" />
              @error('phone')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="checkout-grid">
            <div>
              <label for="address">Street address</label>
              <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Main street 12" required />
              @error('address')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="checkout-grid two-cols">
            <div>
              <label for="city">City</label>
              <input type="text" id="city" name="city" value="{{ old('city') }}" placeholder="Nitrianske Pravno" required />
              @error('city')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="postal">Postal code</label>
              <input type="text" id="postal" name="postal" value="{{ old('postal') }}" placeholder="972 13" required />
              @error('postal')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="checkout-section">
            <h2>Delivery method</h2>

            @foreach ($deliveryOptions as $delivery)
              <label class="checkout-option">
                <input
                  type="radio"
                  name="delivery_id"
                  value="{{ $delivery->id }}"
                  {{ (int) old('delivery_id', $selectedDeliveryId) === (int) $delivery->id ? 'checked' : '' }}
                  required
                />
                <span>
                  {{ $delivery->nazov }} — €{{ number_format((float) $delivery->cena, 2, ',', '') }}
                  @if ($delivery->odhad_dni)
                    ({{ $delivery->odhad_dni }} days)
                  @endif
                </span>
              </label>
            @endforeach

            @error('delivery_id')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="checkout-section">
            <h2>Payment method</h2>

            @foreach ($paymentOptions as $payment)
              <label class="checkout-option">
                <input
                  type="radio"
                  name="payment_id"
                  value="{{ $payment->id }}"
                  {{ (int) old('payment_id', $selectedPaymentId) === (int) $payment->id ? 'checked' : '' }}
                  required
                />
                <span>
                  {{ $payment->sposob_platby }}
                  @if ((float) $payment->poplatok > 0)
                    + €{{ number_format((float) $payment->poplatok, 2, ',', '') }}
                  @endif
                </span>
              </label>
            @endforeach

            @error('payment_id')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="checkout-grid">
            <div>
              <label for="note">Order note</label>
              <textarea id="note" name="note" placeholder="Optional note for your order">{{ old('note') }}</textarea>
              @error('note')
                <p class="form-error">{{ $message }}</p>
              @enderror
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

        @if ((float) $paymentFee > 0)
          <div class="checkout-summary-item">
            <span>Payment fee</span>
            <span>€{{ number_format((float) $paymentFee, 2, ',', '') }}</span>
          </div>
        @endif

        <div class="checkout-summary-total">
          <span>Total</span>
          <span>€{{ number_format((float) $total, 2, ',', '') }}</span>
        </div>
      </aside>
    </div>
  </main>
@endsection
