@extends('layouts.store')

@section('title', 'Products - Sticker Shop')

@section('content')
  @php
    $realProductsCount = $products->count();
  @endphp

  <main class="product-page container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a> &gt; All</div>

    <div class="product-header">
      <h1 class="product-header-text">All Products</h1>
    </div>

    <div class="product-filters">
      <div class="filter-group">
        <label for="filter-category">Category</label>
        <select id="filter-category" name="category">
          <option value="all">All</option>
          <option value="stickers">Stickers</option>
          <option value="pins">Pins</option>
          <option value="patches">Patches</option>
          <option value="plushies">Plushies</option>
        </select>
      </div>

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
            href="{{ route('products.show', ['productId' => $product->id]) }}"
            id="product-{{ $product->id }}"
            class="product-card"
            data-name="{{ $product->nazov }}"
            data-price="{{ number_format((float) $product->cena, 2, '.', '') }}"
            data-order="{{ $product->sort_order }}"
            data-stock="{{ $product->stock_status }}"
            data-category="{{ \Illuminate\Support\Str::lower($product->kategoria_nazov ?: 'uncategorized') }}"
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

      </div>

      <div class="pagination" id="products-pagination">
      </div>
    </section>
  </main>

  <script>
    const productsPerPage = 12;
    const sortSelect = document.getElementById('filter-sort');
    const categorySelect = document.getElementById('filter-category');
    const availabilitySelect = document.getElementById('filter-availability');
    const grid = document.querySelector('.product-page-contents-grid');
    const pagination = document.getElementById('products-pagination');
    const countEl = document.getElementById('product-count');
    let currentPage = 1;

    if (sortSelect && categorySelect && availabilitySelect && grid && countEl && pagination) {
      const allCards = Array.from(grid.querySelectorAll('.product-card'));
      const realCards = allCards.filter((card) => card.dataset.name && card.dataset.price);

      function createPlaceholderCard() {
        const placeholder = document.createElement('div');
        placeholder.className = 'product-card empty-card';
        placeholder.setAttribute('aria-hidden', 'true');
        placeholder.innerHTML = `
          <div class="product-image"></div>
          <div class="product-info">
            <p>Description</p>
            <p>Price</p>
          </div>
        `;

        return placeholder;
      }

      function renderPagination(totalPages) {
        pagination.innerHTML = '';

        if (totalPages <= 1) {
          return;
        }

        const prevBtn = document.createElement('button');
        prevBtn.className = 'page-btn prev-btn';
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', () => {
          if (currentPage > 1) {
            currentPage -= 1;
            updateProducts();
          }
        });
        pagination.appendChild(prevBtn);

        for (let page = 1; page <= totalPages; page += 1) {
          const btn = document.createElement('button');
          btn.className = `page-btn${page === currentPage ? ' active' : ''}`;
          btn.textContent = String(page);
          btn.addEventListener('click', () => {
            currentPage = page;
            updateProducts();
          });
          pagination.appendChild(btn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = 'page-btn next-btn';
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', () => {
          if (currentPage < totalPages) {
            currentPage += 1;
            updateProducts();
          }
        });
        pagination.appendChild(nextBtn);
      }

      function updateProducts() {
        let filtered = [...realCards];

        if (categorySelect.value !== 'all') {
          filtered = filtered.filter((card) => card.dataset.category === categorySelect.value);
        }

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

        const totalPages = Math.max(1, Math.ceil(filtered.length / productsPerPage));
        currentPage = Math.min(currentPage, totalPages);

        const start = (currentPage - 1) * productsPerPage;
        const currentCards = filtered.slice(start, start + productsPerPage);

        grid.innerHTML = '';

        currentCards.forEach((card) => grid.appendChild(card));

        const missingSlots = Math.max(0, productsPerPage - currentCards.length);
        for (let i = 0; i < missingSlots; i += 1) {
          grid.appendChild(createPlaceholderCard());
        }

        countEl.textContent = String(filtered.length);
        renderPagination(totalPages);
      }

      sortSelect.addEventListener('change', () => {
        currentPage = 1;
        updateProducts();
      });
      categorySelect.addEventListener('change', () => {
        currentPage = 1;
        updateProducts();
      });
      availabilitySelect.addEventListener('change', () => {
        currentPage = 1;
        updateProducts();
      });

      updateProducts();
    }
  </script>
@endsection
