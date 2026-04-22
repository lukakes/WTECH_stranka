@extends('layouts.store')

@section('title', 'Products - Sticker Shop')

@section('content')
  <main class="product-page container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a> &gt; All</div>

    <div class="product-header">
      <h1 class="product-header-text">All Products</h1>
    </div>

    <form method="GET" action="{{ route('products') }}" class="product-filters">
      <input type="hidden" name="q" value="{{ $filters['q'] }}">

      <div class="filter-group">
        <label for="filter-category">Category</label>
        <select id="filter-category" name="category">
          <option value="all" {{ $filters['category'] === 'all' ? 'selected' : '' }}>All</option>
          @foreach ($categoryOptions as $slug => $label)
            <option value="{{ $slug }}" {{ $filters['category'] === $slug ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="filter-group">
        <label for="filter-availability">Availability</label>
        <select id="filter-availability" name="availability">
          <option value="all" {{ $filters['availability'] === 'all' ? 'selected' : '' }}>All</option>
          <option value="in-stock" {{ $filters['availability'] === 'in-stock' ? 'selected' : '' }}>In Stock</option>
          <option value="out-of-stock" {{ $filters['availability'] === 'out-of-stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
      </div>

      <div class="filter-group">
        <label for="filter-sort">Sort by</label>
        <select id="filter-sort" name="sort">
          <option value="featured" {{ $filters['sort'] === 'featured' ? 'selected' : '' }}>Featured</option>
          <option value="name-asc" {{ $filters['sort'] === 'name-asc' ? 'selected' : '' }}>Name: A-Z</option>
          <option value="name-desc" {{ $filters['sort'] === 'name-desc' ? 'selected' : '' }}>Name: Z-A</option>
          <option value="price-asc" {{ $filters['sort'] === 'price-asc' ? 'selected' : '' }}>Price: Low to High</option>
          <option value="price-desc" {{ $filters['sort'] === 'price-desc' ? 'selected' : '' }}>Price: High to Low</option>
          <option value="newest" {{ $filters['sort'] === 'newest' ? 'selected' : '' }}>Newest</option>
        </select>
      </div>

      <div class="filter-group">
        <button type="submit" class="btn">Apply</button>
        <a href="{{ route('products') }}" class="cart-link-back-to-shop">Reset</a>
      </div>

      <p class="filter-count">Showing {{ $realProductsCount }} products</p>
    </form>

    <section class="product-page-contents">
      <div class="product-page-contents-grid">
        @forelse ($products as $product)
          <a
            href="{{ route('products.show', ['productId' => $product->id]) }}"
            id="product-{{ $product->id }}"
            class="product-card"
          >
            <div class="product-image">
              <img src="{{ asset($product->image_path) }}" alt="{{ $product->nazov }}">
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

      @if ($products->lastPage() > 1)
        <nav class="pagination" id="products-pagination" aria-label="Products pagination">
          @if ($products->onFirstPage())
            <span class="page-btn prev-btn" aria-disabled="true"><i class="fa-solid fa-chevron-left"></i></span>
          @else
            <a href="{{ $products->previousPageUrl() }}" class="page-btn prev-btn" aria-label="Previous page">
              <i class="fa-solid fa-chevron-left"></i>
            </a>
          @endif

          @for ($page = 1; $page <= $products->lastPage(); $page++)
            <a
              href="{{ $products->url($page) }}"
              class="page-btn{{ $products->currentPage() === $page ? ' active' : '' }}"
              aria-label="Page {{ $page }}"
              {{ $products->currentPage() === $page ? 'aria-current=page' : '' }}
            >
              {{ $page }}
            </a>
          @endfor

          @if ($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}" class="page-btn next-btn" aria-label="Next page">
              <i class="fa-solid fa-chevron-right"></i>
            </a>
          @else
            <span class="page-btn next-btn" aria-disabled="true"><i class="fa-solid fa-chevron-right"></i></span>
          @endif
        </nav>
      @endif
    </section>
  </main>
@endsection
