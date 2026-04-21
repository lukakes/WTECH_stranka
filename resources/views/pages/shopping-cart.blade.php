@extends('layouts.store')

@section('title', 'Shopping Cart - Sticker Shop')

@section('content')
  <main class="shopping-cart container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> &gt; Shopping Cart
    </div>

    <div class="shopping-cart-header">
      <h1>Shopping cart</h1>
    </div>

    <div class="shopping-cart-contents">
      @if (session('cart_success'))
        <p class="form-success">{{ session('cart_success') }}</p>
      @endif

      @if (session('cart_error'))
        <p class="form-error">{{ session('cart_error') }}</p>
      @endif

      <ul class="cart-items">
        <li class="cart-item shopping-flex">
          <span class="description">Product</span>
          <span class="price">Price</span>
          <span class="quantity">Quantity</span>
          <span class="total-price">Total</span>
        </li>

        @forelse ($cartItems as $item)
          <li class="cart-item-container">
            <div class="cart-item shopping-flex">
              <div class="image">
                <a href="{{ route('products.show', ['productId' => $item->product_id]) }}">
                  <div class="product-image">
                    <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}">
                  </div>
                </a>
              </div>

              <div class="description">
                <a href="{{ route('products.show', ['productId' => $item->product_id]) }}">{{ $item->name }}</a>
              </div>

              <div class="price">
                <div class="price-text">€{{ number_format((float) $item->price, 2, ',', '') }}</div>
              </div>

              <div class="quantity">
                <form method="POST" action="{{ route('cart.update') }}" class="quantity-change">
                  @csrf
                  <input type="hidden" name="variant_id" value="{{ $item->variant_id }}">

                  <button class="minus round-btn" type="button" aria-label="Decrease quantity">
                    <i class="fa-solid fa-minus"></i>
                  </button>

                  <input
                    type="number"
                    name="quantity"
                    value="{{ $item->quantity }}"
                    min="1"
                    max="{{ max(1, $item->max_stock) }}"
                    class="cart-prod-number"
                    aria-label="Quantity"
                  >

                  <button class="plus round-btn" type="button" aria-label="Increase quantity">
                    <i class="fa-solid fa-plus"></i>
                  </button>
                </form>

                <div class="quantity-remove">
                  <form method="POST" action="{{ route('cart.remove') }}">
                    @csrf
                    <input type="hidden" name="variant_id" value="{{ $item->variant_id }}">
                    <button type="submit" class="remove-cart-item">
                      <i class="fa-solid fa-x"></i>
                      <span>Remove</span>
                    </button>
                  </form>
                </div>
              </div>

              <div class="total-price">
                <div class="total-price-text">€{{ number_format((float) $item->line_total, 2, ',', '') }}</div>
              </div>
            </div>
          </li>
        @empty
          <li class="cart-item-container">
            <div class="cart-item shopping-flex">
              <div class="description">Your cart is empty.</div>
            </div>
          </li>
        @endforelse

        <li class="cart-items">
          <div class="cart-subtotal">
            <div class="subtotal-info">
              Taxes and <a href="#" class="cart-shipping-info">shipping</a> calculated at checkout
            </div>
            <div class="subtotal-value">
              <div class="subtotal-value-text">Subtotal:</div>
              <div class="subtotal-value-number">€{{ number_format((float) $subtotal, 2, ',', '') }}</div>
            </div>
          </div>
        </li>
      </ul>

      <div class="cart-payment">
        <a href="#" class="btn">Check out</a>
        <a href="{{ route('products') }}" class="cart-link-back-to-shop">Continue shopping</a>
      </div>
    </div>
  </main>

  <script>
    document.querySelectorAll('.quantity-change').forEach((form) => {
      const input = form.querySelector('.cart-prod-number');
      const minus = form.querySelector('.minus');
      const plus = form.querySelector('.plus');

      if (!input || !minus || !plus) {
        return;
      }

      minus.addEventListener('click', () => {
        const min = parseInt(input.min, 10) || 1;
        const current = parseInt(input.value, 10) || min;
        input.value = String(Math.max(min, current - 1));
        form.submit();
      });

      plus.addEventListener('click', () => {
        const max = parseInt(input.max, 10) || 99;
        const min = parseInt(input.min, 10) || 1;
        const current = parseInt(input.value, 10) || min;
        input.value = String(Math.min(max, current + 1));
        form.submit();
      });

      input.addEventListener('change', () => {
        const max = parseInt(input.max, 10) || 99;
        const min = parseInt(input.min, 10) || 1;
        const current = parseInt(input.value, 10) || min;
        input.value = String(Math.min(max, Math.max(min, current)));
        form.submit();
      });
    });
  </script>
@endsection
