@props([
  'filters' => [],
  'categoryOptions' => [],
  'realProductsCount' => 0,
])

<div class="breadcrumb">
  <a href="{{ route('home') }}">Home</a>
  &gt;
  {{ ($filters['category'] ?? 'all') !== 'all' && isset($categoryOptions[$filters['category'] ?? '']) ? $categoryOptions[$filters['category']] : 'All Products' }}
</div>

<div class="product-header">
  <h1 class="product-header-text">
    {{ ($filters['category'] ?? 'all') !== 'all' && isset($categoryOptions[$filters['category'] ?? '']) ? $categoryOptions[$filters['category']] : 'All Products' }}
  </h1>
</div>

<form method="GET" action="{{ request()->routeIs('products.category') ? route('products.category', ['categorySlug' => request()->route('categorySlug')]) : route('products') }}" class="product-filters">
  <input type="hidden" name="q" value="{{ $filters['q'] }}">

  <div class="filter-group">
    <label for="filter-category">Category</label>
    <select id="filter-category" name="category" {{ request()->routeIs('products.category') ? 'disabled' : '' }}>
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

  <div class="filter-group price-filter-group">
    <input type="hidden" name="min_price" value="0">
    <label for="filter-max-price">Price up to</label>
    <input
      id="filter-max-price"
      name="max_price"
      type="range"
      min="0"
      max="50"
      step="1"
      value="{{ $filters['max_price'] }}"
    >
    <span id="filter-max-price-label">€{{ $filters['max_price'] }}</span>
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
    <a href="{{ request()->routeIs('products.category') ? route('products.category', ['categorySlug' => request()->route('categorySlug')]) : route('products') }}" class="cart-link-back-to-shop">Reset</a>
  </div>

  <p class="filter-count">Showing {{ $realProductsCount }} products</p>
  @if (request()->routeIs('products.category'))
    <p class="filter-count">Category is locked for this page.</p>
  @endif
</form>

<script>
  const priceSlider = document.getElementById('filter-max-price');
  const priceLabel = document.getElementById('filter-max-price-label');

  if (priceSlider && priceLabel) {
    priceSlider.addEventListener('input', () => {
      priceLabel.textContent = '€' + priceSlider.value;
    });
  }
</script>
