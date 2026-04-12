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
  <header class="top-header">
    <h1 class="logo">Super nazov</h1>
    <div class="header-row">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search the store" />
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

        <a href="{{ route('home') }}#featured-products" class="cart-icon" aria-label="Shopping cart">
          <i class="fa-solid fa-cart-shopping"></i>
          <span class="cart-count">0</span>
        </a>
      </div>
    </div>
  </header>

  <nav class="navbar">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
    <div class="dropdown">
      <a href="{{ route('products') }}" class="{{ request()->routeIs('products') ? 'active' : '' }}">Shop <i class="fa-solid fa-chevron-down nav-arrow"></i></a>
      <div class="dropdown-content">
        <div class="row">
          <div class="column">
            <h3>Category 1</h3>
            <a href="{{ route('products') }}">Link 1</a>
            <a href="{{ route('products') }}">Link 2</a>
            <a href="{{ route('products') }}">Link 3</a>
          </div>
          <div class="column">
            <h3>Category 2</h3>
            <a href="{{ route('products') }}">Link 1</a>
            <a href="{{ route('products') }}">Link 2</a>
            <a href="{{ route('products') }}">Link 3</a>
          </div>
          <div class="column">
            <h3>Category 3</h3>
            <a href="{{ route('products') }}">Link 1</a>
            <a href="{{ route('products') }}">Link 2</a>
            <a href="{{ route('products') }}">Link 3</a>
          </div>
          <div class="dropdown-image">
            <img src="https://mkskimgmodrykonik.vshcdn.net/0Xv0iZlre0O_s1600x1600.jpg" alt="Pytajte sa odpoviem, Fidlibum som vsetko viem">
            <p><a href="{{ route('home') }}#contact-shop">Pytajte sa</a> vsetko viem, Fidlibum som vsetko viem!</p>
          </div>
        </div>
        <div class="row">
          <a href="{{ route('products') }}">See all products</a>
        </div>
      </div>
    </div>
    <a href="{{ route('home') }}#about-shop">About</a>
    <a href="{{ route('home') }}#contact-shop">Contact</a>
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