<div class="dropdown">
  <a href="{{ route('products') }}" class="{{ request()->routeIs('products') || request()->routeIs('products.category') ? 'active' : '' }}">
    Shop <i class="fa-solid fa-chevron-down nav-arrow"></i>
  </a>
  <div class="dropdown-content">
    <div class="row">
      <div class="column">
        <h3>Categories</h3>
        <a href="{{ route('products.category', ['categorySlug' => 'stickers']) }}">Stickers</a>
        <a href="{{ route('products.category', ['categorySlug' => 'pins']) }}">Pins</a>
        <a href="{{ route('products.category', ['categorySlug' => 'patches']) }}">Patches</a>
        <a href="{{ route('products.category', ['categorySlug' => 'plushies']) }}">Plushies</a>
      </div>
      <div class="column">
        <h3>Product Types</h3>
        <a href="{{ route('products.category', ['categorySlug' => 'stickers']) }}">Vinyl Stickers</a>
        <a href="{{ route('products.category', ['categorySlug' => 'pins']) }}">Enamel Pins</a>
        <a href="{{ route('products.category', ['categorySlug' => 'patches']) }}">Iron-on Patches</a>
        <a href="{{ route('products.category', ['categorySlug' => 'plushies']) }}">Soft Plushies</a>
      </div>
      <div class="column">
        <h3>Quick Links</h3>
        <a href="{{ route('products', ['availability' => 'in-stock']) }}">In stock</a>
        <a href="{{ route('products', ['sort' => 'newest']) }}">Newest</a>
      </div>
      <div class="dropdown-image">
        <img src="https://mkskimgmodrykonik.vshcdn.net/0Xv0iZlre0O_s1600x1600.jpg" alt="Pytajte sa odpoviem, Fidlibum som vsetko viem">
        <p><a href="{{ route('contact') }}">Pytajte sa</a> vsetko viem, Fidlibum som vsetko viem!</p>
      </div>
    </div>
    <div class="row">
      <a href="{{ route('products') }}">See all products</a>
    </div>
  </div>
</div>
