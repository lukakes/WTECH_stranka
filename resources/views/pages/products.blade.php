@extends('layouts.store')

@section('title', 'Products - Sticker Shop')

@section('content')
  @php
    $totalGridSlots = 12;
    $realProductsCount = $products->count();
    $emptySlots = max(0, $totalGridSlots - $realProductsCount);
  @endphp

  <main class="product-page container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a> &gt; All</div>

    <div class="product-header">
      <h1 class="product-header-text">All Products</h1>
    </div>

    <div class="product-filters">
      <div class="filter-group">
        <label for="filter-availability">Availability</label>
        <select id="filter-availability" name="availability">
          <option value="all">All</option>
          <option value="in-stock">In Stock</option>
          <option value="out-of-stock">Out of Stock</option>
        </select>
      </div>

      <div class="filter-group">
        <label for="filter-sort">Sort by</label>
        <select id="filter-sort" name="sort">
          <option value="featured">Featured</option>
          <option value="name-asc">Name: A-Z</option>
          <option value="name-desc">Name: Z-A</option>
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
          <option value="newest">Newest</option>
        </select>
      </div>

      <p class="filter-count">Showing <span id="product-count">{{ $realProductsCount }}</span> products</p>
    </div>

    <section class="product-page-contents">
      <div class="product-page-contents-grid">
        @forelse ($products as $product)
          @php
            $imagePath = $product->image_url ?: 'images/Products/prod-img-1.png';
            $imagePath = preg_replace('/^\.\.\//', '', (string) $imagePath);
          @endphp

          <a
            href="{{ route('products') }}#product-{{ $product->id }}"
            id="product-{{ $product->id }}"
            class="product-card"
            data-name="{{ $product->nazov }}"
            data-price="{{ number_format((float) $product->cena, 2, '.', '') }}"
            data-order="{{ $product->sort_order }}"
            data-stock="{{ $product->stock_status }}"
          >
            <div class="product-image">
              <img src="{{ asset($imagePath) }}" alt="{{ $product->nazov }}">
            </div>
            <div class="product-info">
              <p>{{ $product->nazov }}</p>
              <p>€{{ number_format((float) $product->cena, 2, ',', '') }}</p>
            </div>
          </a>
        @empty
          <p>No products found.</p>
        @endforelse

        @for ($i = 0; $i < $emptySlots; $i++)
          <div class="product-card empty-card" aria-hidden="true">
            <div class="product-image"></div>
            <div class="product-info">
              <p>Description</p>
              <p>Price</p>
            </div>
          </div>
        @endfor
      </div>

      <div class="pagination" aria-hidden="true">
        <button class="page-btn prev-btn" disabled>
          <i class="fa-solid fa-chevron-left"></i>
        </button>

        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">3</button>
        <span class="page-dots">...</span>
        <button class="page-btn">8</button>

        <button class="page-btn next-btn">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </section>
  </main>

  <script>
    const sortSelect = document.getElementById('filter-sort');
    const availabilitySelect = document.getElementById('filter-availability');
    const grid = document.querySelector('.product-page-contents-grid');
    const countEl = document.getElementById('product-count');

    if (sortSelect && availabilitySelect && grid && countEl) {
      const allCards = Array.from(grid.querySelectorAll('.product-card'));
      const realCards = allCards.filter((card) => card.dataset.name && card.dataset.price);
      const placeholders = allCards.filter((card) => !card.dataset.name || !card.dataset.price);

      function updateProducts() {
        let filtered = [...realCards];

        if (availabilitySelect.value === 'in-stock') {
          filtered = filtered.filter((card) => card.dataset.stock === 'in-stock');
        } else if (availabilitySelect.value === 'out-of-stock') {
          filtered = filtered.filter((card) => card.dataset.stock === 'out-of-stock');
        }

        filtered.sort((a, b) => {
          const nameA = a.dataset.name.toLowerCase();
          const nameB = b.dataset.name.toLowerCase();
          const priceA = parseFloat(a.dataset.price);
          const priceB = parseFloat(b.dataset.price);
          const orderA = parseInt(a.dataset.order, 10) || 0;
          const orderB = parseInt(b.dataset.order, 10) || 0;

          switch (sortSelect.value) {
            case 'name-asc':
              return nameA.localeCompare(nameB);
            case 'name-desc':
              return nameB.localeCompare(nameA);
            case 'price-asc':
              return priceA - priceB;
            case 'price-desc':
              return priceB - priceA;
            case 'newest':
              return orderB - orderA;
            case 'featured':
            default:
              return orderA - orderB;
          }
        });

        grid.innerHTML = '';
        filtered.forEach((card) => grid.appendChild(card));
        placeholders.forEach((card) => grid.appendChild(card));

        countEl.textContent = String(filtered.length);
      }

      sortSelect.addEventListener('change', updateProducts);
      availabilitySelect.addEventListener('change', updateProducts);

      updateProducts();
    }
  </script>
@endsection
