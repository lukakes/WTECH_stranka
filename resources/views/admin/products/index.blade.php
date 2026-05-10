@extends('layouts.store')

@section('title', 'Admin products - Sticker Shop')

@section('content')
  <main class="admin-page container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> &gt; Admin &gt; Products
    </div>

    <section class="admin-panel">
      <div class="admin-header">
        <div>
          <h1>Products</h1>
          <p>Manage product catalog, stock, categories, and images.</p>
        </div>

        <a href="{{ route('admin.products.create') }}" class="btn">Add product</a>
      </div>

      @if (session('admin_success'))
        <p class="form-success admin-message">{{ session('admin_success') }}</p>
      @endif
      @if (session('admin_error'))
        <p class="form-error admin-form-alert">{{ session('admin_error') }}</p>
      @endif

      <form method="GET" action="{{ route('admin.products.index') }}" class="admin-toolbar">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search products">
        <button type="submit" class="btn">Search</button>
        @if ($search !== '')
          <a href="{{ route('admin.products.index') }}" class="cart-link-back-to-shop">Reset</a>
        @endif
      </form>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Image</th>
              <th>Product</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($products as $product)
              @php
                $variant = $product->variants->first();
                $stock = (int) $product->variants->sum('skladom');
                $imagePath = ltrim((string) $product->images->first()?->url, '/');
                $imagePath = $imagePath !== '' ? $imagePath : 'images/Products/prod-img-1.png';
              @endphp

              <tr>
                <td>
                  <img src="{{ asset($imagePath) }}" alt="{{ $product->nazov }}" class="admin-product-thumb">
                </td>
                <td>
                  <strong>{{ $product->nazov }}</strong>
                  <span class="admin-muted">#{{ $product->id }}</span>
                </td>
                <td>{{ $product->category?->nazov ?? 'Uncategorized' }}</td>
                <td>€{{ number_format((float) ($variant?->cena ?? $product->zakladna_cena ?? 0), 2, ',', '') }}</td>
                <td>{{ $stock }}</td>
                <td>
                  <span class="admin-status {{ $product->aktivny ? 'is-active' : 'is-inactive' }}">
                    {{ $product->aktivny ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <div class="admin-actions">
                    <a href="{{ route('admin.products.edit', ['product' => $product->id]) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.products.destroy', ['product' => $product->id]) }}" onsubmit="return confirm('Delete this product permanently?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7">No products found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($products->lastPage() > 1)
        <nav class="pagination" aria-label="Admin product pagination">
          @for ($page = 1; $page <= $products->lastPage(); $page++)
            <a
              href="{{ $products->url($page) }}"
              class="page-btn{{ $products->currentPage() === $page ? ' active' : '' }}"
              {{ $products->currentPage() === $page ? 'aria-current=page' : '' }}
            >
              {{ $page }}
            </a>
          @endfor
        </nav>
      @endif
    </section>
  </main>
@endsection
