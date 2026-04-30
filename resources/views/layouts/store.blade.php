<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Sticker Shop')</title>

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
  />
  <link rel="stylesheet" href="{{ asset('style.css') }}" />
</head>
<body class="@yield('bodyClass')">
  @php
    $isProductSearchPage = request()->routeIs('products') || request()->routeIs('products.category');
  @endphp

  <header class="top-header">
    <h1 class="logo">Super nazov</h1>
    <div class="header-row">
      <div class="search-box">
        <form method="GET" action="{{ request()->routeIs('products.category') ? route('products.category', ['categorySlug' => request()->route('categorySlug')]) : route('products') }}" class="store-search-form">
          <i class="fa-solid fa-magnifying-glass"></i>
          @if ($isProductSearchPage)
            @unless (request()->routeIs('products.category'))
              <input type="hidden" name="category" value="{{ request('category', 'all') }}">
            @endunless
            <input type="hidden" name="availability" value="{{ request('availability', 'all') }}">
            <input type="hidden" name="sort" value="{{ request('sort', 'featured') }}">
          @endif
          <input type="text" name="q" id="store-product-search" placeholder="Search the store" autocomplete="off" value="{{ $isProductSearchPage ? request('q', '') : '' }}" />
        </form>
      </div>

      <div class="header-icons">
        <div class="profile-menu">
          <details class="profile-dropdown">
            <summary aria-label="Open profile menu">
              <i class="fa-regular fa-user"></i>
            </summary>
            <div class="profile-dropdown-content">
              @auth
                <a href="{{ route('profile.edit') }}">Profile</a>
                <a href="{{ route('dashboard') }}">Dashboard</a>
                @if (auth()->user()->isAdmin())
                  <a href="{{ route('admin.products.index') }}">Admin products</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit">Logout</button>
                </form>
              @else
                <a href="{{ route('login') }}">Login</a>
                @if (Route::has('register'))
                  <a href="{{ route('register') }}">Register</a>
                @endif
              @endauth
            </div>
          </details>
        </div>

        @php
          $cartCount = auth()->check() && auth()->user()->isAdmin()
            ? 0
            : array_sum(session('cart', []));
        @endphp

        <a href="{{ route('cart.index') }}" class="cart-icon" aria-label="Shopping cart">
          <i class="fa-solid fa-cart-shopping"></i>
          <span class="cart-count">{{ $cartCount }}</span>
        </a>
      </div>
    </div>
  </header>

  <nav class="navbar">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
    <x-store.shop-dropdown />
    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a>
    <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
  </nav>

  @yield('content')

  <footer id="contact-shop">
    <div class="footer-content">
      <div class="footer-links">
        <h2>Footer</h2>
        <p><a href="{{ route('home') }}">Home</a></p>
        <p><a href="{{ route('login') }}">Login</a></p>
        @if (Route::has('register'))
          <p><a href="{{ route('register') }}">Register</a></p>
        @endif
      </div>

      <div class="footer-socials">
        <a href="https://www.facebook.com/ozynet/?locale=sk_SK" target="_blank" rel="noopener"><img src="{{ asset('images/facebook.png') }}" alt="Facebook"></a>
        <a href="https://www.instagram.com/ozynet/" target="_blank" rel="noopener"><img src="{{ asset('images/instagram.png') }}" alt="Instagram"></a>
        <a href="https://x.com" target="_blank" rel="noopener"><img src="{{ asset('images/twitter.png') }}" alt="Twitter"></a>
      </div>
    </div>
  </footer>

  <script>
    const dropdown = document.querySelector('.dropdown');

    if (dropdown) {
      let closeTimer;

      dropdown.addEventListener('mouseenter', () => {
        clearTimeout(closeTimer);
        dropdown.classList.add('open');
      });

      dropdown.addEventListener('mouseleave', () => {
        closeTimer = setTimeout(() => {
          dropdown.classList.remove('open');
        }, 300);
      });
    }
  </script>
</body>
</html>
