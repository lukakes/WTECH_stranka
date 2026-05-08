@extends('layouts.store')

@section('title', 'Edit product - Sticker Shop')

@section('content')
  <main class="admin-page container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> &gt;
      <a href="{{ route('admin.products.index') }}">Admin products</a> &gt;
      Edit product
    </div>

    <section class="admin-panel">
      <div class="admin-header">
        <div>
          <h1>Edit product</h1>
          <p>Update product details and manage product photos.</p>
        </div>

        <a href="{{ route('admin.products.index') }}" class="cart-link-back-to-shop">Back to products</a>
      </div>

      @if (session('admin_success'))
        <p class="form-success admin-message">{{ session('admin_success') }}</p>
      @endif
      @if (session('admin_error'))
        <p class="form-error admin-form-alert">{{ session('admin_error') }}</p>
      @endif
      @if ($errors->any())
        <p class="form-error admin-form-alert">Please check the highlighted fields and try again.</p>
      @endif

      <form method="POST" action="{{ route('admin.products.update', ['product' => $product->id]) }}" enctype="multipart/form-data" class="admin-product-form">
        @csrf
        @method('PATCH')

        <div class="admin-form-grid">
          <div class="admin-form-field">
            <label for="nazov">Name</label>
            <input id="nazov" type="text" name="nazov" value="{{ old('nazov', $product->nazov) }}" required>
            @error('nazov')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="admin-form-field">
            <label for="kategoria_id">Category</label>
            <select id="kategoria_id" name="kategoria_id" required>
              <option value="">Choose category</option>
              @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ (int) old('kategoria_id', $product->kategoria_id) === (int) $category->id ? 'selected' : '' }}>
                  {{ $category->nazov }}
                </option>
              @endforeach
            </select>
            @error('kategoria_id')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="admin-form-field">
            <label for="cena">Price</label>
            <input id="cena" type="number" name="cena" min="0" step="0.01" value="{{ old('cena', $variant?->cena ?? $product->zakladna_cena) }}" required>
            @error('cena')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="admin-form-field">
            <label for="skladom">Stock</label>
            <input id="skladom" type="number" name="skladom" min="0" step="1" value="{{ old('skladom', $variant?->skladom ?? 0) }}" required>
            @error('skladom')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div class="admin-form-field">
          <label for="popis">Description</label>
          <textarea id="popis" name="popis" required>{{ old('popis', $product->popis) }}</textarea>
          @error('popis')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="admin-form-field">
          <label>Current photos</label>
          <div class="admin-existing-images">
            @foreach ($product->images as $image)
              <article class="admin-existing-image">
                <img src="{{ asset($image->url) }}" alt="{{ $product->nazov }}">
                <button
                  type="submit"
                  form="delete-product-image-{{ $image->id }}"
                  aria-label="Remove image"
                  onclick="return confirm('Remove this image?');"
                >
                  <i class="fa-solid fa-xmark"></i>
                </button>
              </article>
            @endforeach
          </div>
        </div>

        <div class="admin-form-field">
          <label for="images">Add more photos</label>
          <label class="admin-upload-box" id="admin-upload-box" for="images">
            <input id="images" type="file" name="images[]" accept="image/*" multiple>
            <span class="admin-upload-icon">
              <i class="fa-regular fa-image"></i>
            </span>
            <span class="admin-upload-title">Upload photos</span>
            <small>Drag and drop images here, or click to choose files.</small>
          </label>
          <div class="admin-upload-preview" id="admin-upload-preview" aria-live="polite"></div>
          @error('images')
            <p class="form-error">{{ $message }}</p>
          @enderror
          @error('images.*')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <label class="admin-checkbox">
          <input type="checkbox" name="aktivny" value="1" {{ old('aktivny', $product->aktivny) ? 'checked' : '' }}>
          <span>Product is active and visible in the shop</span>
        </label>

        <div class="admin-form-actions">
          <a href="{{ route('admin.products.index') }}" class="cart-link-back-to-shop">Cancel</a>
          <button type="submit" class="btn">Save product</button>
        </div>
      </form>

      @foreach ($product->images as $image)
        <form
          id="delete-product-image-{{ $image->id }}"
          method="POST"
          action="{{ route('admin.products.images.destroy', ['product' => $product->id, 'image' => $image->id]) }}"
        >
          @csrf
          @method('DELETE')
        </form>
      @endforeach
    </section>
  </main>

  @include('admin.products.partials.upload-preview-script')
@endsection
