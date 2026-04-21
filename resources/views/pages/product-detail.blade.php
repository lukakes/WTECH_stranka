@extends('layouts.store')

@section('title', ($product->nazov ?? 'Product') . ' - Sticker Shop')

@section('content')
  <main class="product-detail-page container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a>
      &gt;
      <a href="{{ route('products') }}">Shop</a>
      &gt;
      {{ $product->nazov }}
    </div>

    <section class="product-detail-layout">
      <div class="product-detail-image-wrap">
        <div class="product-detail-image">
          <img src="{{ asset($product->image_path) }}" alt="{{ $product->nazov }}">
        </div>
      </div>

      <div class="product-detail-info">
        <h1 class="product-detail-title">{{ $product->nazov }}</h1>
        <p class="product-detail-price">€{{ number_format((float) $product->cena, 2, ',', '') }}</p>

        @if (session('cart_error'))
          <p class="form-error">{{ session('cart_error') }}</p>
        @endif

        <form class="product-detail-actions" method="POST" action="{{ route('cart.add') }}">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">

          <div class="quantity-picker">
            <button type="button" class="quantity-btn minus">-</button>
            <input type="number" name="quantity" class="quantity-value" value="1" min="1" max="99">
            <button type="button" class="quantity-btn plus">+</button>
          </div>

          <button class="detail-outline-btn" type="button">Add to wishlist</button>
          <button class="detail-cart-btn" type="submit">Add to Cart</button>
        </form>

        <div class="product-detail-description">
          <p>{{ $product->description_text }}</p>
        </div>

        <div class="product-detail-specs">
          <h2>Product details</h2>
          <ul>
            <li>Category: {{ $product->kategoria_nazov ?: 'Uncategorized' }}</li>
            <li>Availability: {{ $product->stock_status }}</li>
            <li>Available quantity: {{ $product->stock_total }}</li>
            <li>Price: €{{ number_format((float) $product->cena, 2, ',', '') }}</li>
            <li>SKU: PROD-{{ str_pad((string) $product->id, 4, '0', STR_PAD_LEFT) }}</li>
          </ul>
        </div>
      </div>
    </section>

    <section class="product-review-summary">
      <div class="review-left">
        <span class="review-stars">★★★★★</span>
        <span class="review-score">5.00 out of 5</span>
        <p>Based on 67 reviews</p>
      </div>

      <button class="btn" type="button">Write a review</button>
    </section>
  </main>

  <section class="product-reviews-list">
    <article class="review-card">
      <div class="review-card-stars">★★★★★</div>
      <h3>FUHA 10/10</h3>
      <p class="review-text">NAJLEPSIA VEC AKU SOM KEDY KUPILA !!!!!11!!!!!</p>

      <div class="review-author">
        <div class="review-avatar">B</div>
        <div>
          <p class="review-name">Ben doverova</p>
          <span class="review-date">8.3.2026</span>
        </div>
      </div>
    </article>

    <article class="review-card">
      <div class="review-card-stars">★☆☆☆☆</div>
      <h3>Som sklamana</h3>
      <p class="review-text">Po objednani mi namiesto pinu na obrazku prisla kniha zavarame na chalupe.</p>

      <div class="review-author">
        <div class="review-avatar">K</div>
        <div>
          <p class="review-name">Kim</p>
          <span class="review-date">9.3.2026</span>
        </div>
      </div>
    </article>

    <article class="review-card">
      <div class="review-card-stars">☆☆☆☆☆</div>
      <h3>Cele zle</h3>
      <p class="review-text">
        Este v ziadnych potravinach som sa nestretla s takymto personalom!!! Neochotni,
        arogantni a povysenecki.
      </p>

      <div class="review-author">
        <div class="review-avatar">S</div>
        <div>
          <p class="review-name">Simona Szalayova</p>
          <span class="review-date">7.2.2026</span>
        </div>
      </div>
    </article>
  </section>

  <script>
    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');
    const input = document.querySelector('.quantity-value');

    if (minusBtn && plusBtn && input) {
      minusBtn.addEventListener('click', () => {
        const value = parseInt(input.value, 10) || 1;
        if (value > 1) {
          input.value = String(value - 1);
        }
      });

      plusBtn.addEventListener('click', () => {
        const value = parseInt(input.value, 10) || 1;
        const max = parseInt(input.max, 10) || 99;

        if (value < max) {
          input.value = String(value + 1);
        }
      });
    }
  </script>
@endsection
