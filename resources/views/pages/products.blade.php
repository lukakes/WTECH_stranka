@extends('layouts.store')

@section('title', 'Products - Sticker Shop')

@section('content')
  <main class="product-page container">
    <x-store.products-filters
      :filters="$filters"
      :category-options="$categoryOptions"
      :real-products-count="$realProductsCount"
    />

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
