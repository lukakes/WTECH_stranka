@extends('layouts.store')

@section('title', 'Home - Sticker Shop')

@section('content')

  <section class="hero-area">
    <section class="hero">
      <div class="hero-popup">
        <h1>Welcome</h1>
        <p>Don't wait around for other people to grab your favorite pieces!</p>
        <a href="{{ route('products', ['sort' => 'featured', 'availability' => 'in-stock']) }}" class="btn">Shop now</a>
      </div>
      <div class="hero-grid">
        <img src="{{ asset('images/Homepage/cats/cat1.png') }}" alt="cat sticker">
        <img src="{{ asset('images/Homepage/cats/cat2.png') }}" alt="cat sticker">
        <img src="{{ asset('images/Homepage/cats/cat3.png') }}" alt="cat sticker">
        <img src="{{ asset('images/Homepage/cats/cat4.png') }}" alt="cat sticker">
        <img src="{{ asset('images/Homepage/cats/cat5.png') }}" alt="cat sticker">
        <img src="{{ asset('images/Homepage/cats/cat6.png') }}" alt="cat sticker">
      </div>
    </section>
  </section>

  <section class="featured-section" id="featured-products">
    <h2>Featured</h2>
    <div class="featured-grid container">
      @forelse($featuredProducts as $product)
        <x-product-card
          :href="route('products.show', $product->id) ?: '#featured-products'"
          :image="$product->image_url ?: 'images/Products/prod-img-1.png'"
          :name="$product->nazov ?: 'Product'"
          :price="$product->cena ?: 0"
        />
      @empty
        <p>No featured products found.</p>
      @endforelse
    </div>
  </section>

  <section class="promo-section">
    <div class="promo-content container">
      <div class="promo-text">
        <h2>Squidward plush from SpongeBob SquarePants</h2>
        <p>
          Soft, expressive, and just dramatic enough for any shelf.
          This Squidward plush is made for cozy rooms, collector corners,
          and everyone who understands his very specific mood.
        </p>
        <a href="{{ route('products') }}" class="btn">See more !</a>
      </div>

      <div class="promo-image">
        <img src="{{ asset('images/Homepage/Squidward plush.png') }}" alt="Squidward plush">
      </div>
    </div>
  </section>

  <section class="promo-section">
    <div class="promo-content reverse container">
      <div class="promo-text">
        <h2>Demon Slayer anime pins</h2>
        <p>
          Add a small flash of your favorite series to your backpack,
          jacket, or pin board. These Demon Slayer pins are compact,
          colorful, and easy to mix with the rest of your collection.
        </p>
        <a href="{{ route('products') }}" class="btn">See more !</a>
      </div>

      <div class="promo-image">
        <img src="{{ asset('images/Homepage/Demon Slayer pins.png') }}" alt="Demon Slayer pins">
      </div>
    </div>
  </section>

  <section class="bottom-space" id="about-shop">
    <div class="bottom-text-row">
      <div class="bottom-text-left-container">
        <h2>Tiny treasures</h2>
        <p>
          Every order is inspected by our imaginary quality committee,
          which mostly judges whether the stickers look cute enough
          to survive on a laptop.
        </p>
      </div>
      <div class="bottom-text-center-container">
        <h2>Shelf drama</h2>
        <p>
          Our plushies are emotionally prepared for long naps,
          dramatic desk poses, and being silently supportive during
          deadline season.
        </p>
      </div>
      <div class="bottom-text-right-container">
        <h2>Pin power</h2>
        <p>
          The pins have not officially unlocked any anime abilities,
          but backpacks wearing them have been reported to look
          at least 42 percent more legendary.
        </p>
      </div>
    </div>
  </section>

@endsection
