<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Sticker Shop')</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('style.css') }}" />
</head>
<body>

  {{-- Header --}}
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
              <a href="#">Login</a>
              <a href="#">Profile</a>
              <a href="#">Logout</a>
            </div>
          </details>
        </div>
        <a href="#" class="cart-icon">
          <i class="fa-solid fa-cart-shopping"></i>
          <span class="cart-count">0</span>
        </a>
      </div>
    </div>
  </header>

  {{-- Nav --}}
  <nav class="navbar">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
    <div class="dropdown">
      <a href="#">Shop <i class="fa-solid fa-chevron-down nav-arrow"></i></a>
      <div class="dropdown-content">
        <div class="row">
          <div class="column">
            <h3>Category 1</h3>
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
          </div>
          <div class="column">
            <h3>Category 2</h3>
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
          </div>
          <div class="column">
            <h3>Category 3</h3>
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
          </div>
          <div class="dropdown-image">
            <img src="https://mkskimgmodrykonik.vshcdn.net/0Xv0iZlre0O_s1600x1600.jpg" alt="Fidlibum" />
            <p><a href="#">Pytajte sa</a> všetko viem, Fidlibum som všetko viem!</p>
          </div>
        </div>
        <div class="row">
          <a href="#">See all products</a>
        </div>
      </div>
    </div>
    <a href="#">About</a>
    <a href="#">Contact</a>
  </nav>

  {{-- Page content goes here --}}
  <main>
    @yield('content')
  </main>

  <footer>
    <div class="footer-content">
      <div class="footer-links">
        <h2>Footer</h2>
        <p>Link</p>
        <p>Link</p>
        <p>Link</p>
      </div>

      <div class="footer-socials">
        <a href="#"><img src="{{asset('/images/facebook.png')}}" alt="Facebook"></a>
        <a href="#"><img src="{{asset('/images/instagram.png')}}" alt="Instagram"></a>
        <a href="#"><img src="{{asset('/images/twitter.png')}}" alt="Twitter"></a>
      </div>
    </div>
  </footer>

  <script>
    const dropdown = document.querySelector('.dropdown');
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
  </script>

</body>
</html>