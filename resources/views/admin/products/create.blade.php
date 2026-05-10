@extends('layouts.store')

@section('title', 'Create product - Sticker Shop')

@section('content')
  <main class="admin-page container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> &gt;
      <a href="{{ route('admin.products.index') }}">Admin products</a> &gt;
      Create product
    </div>

    <section class="admin-panel">
      <div class="admin-header">
        <div>
          <h1>Create product</h1>
          <p>Add a catalog item with stock, category, and at least two photos.</p>
        </div>

        <a href="{{ route('admin.products.index') }}" class="cart-link-back-to-shop">Back to products</a>
      </div>

      @if ($errors->any())
        <p class="form-error admin-form-alert">Please check the highlighted fields and try again.</p>
      @endif

      <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="admin-product-form">
        @csrf

        <div class="admin-form-grid">
          <div class="admin-form-field">
            <label for="nazov">Name</label>
            <input id="nazov" type="text" name="nazov" value="{{ old('nazov') }}" required>
            @error('nazov')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="admin-form-field">
            <label for="kategoria_id">Category</label>
            <select id="kategoria_id" name="kategoria_id" required>
              <option value="">Choose category</option>
              @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ (int) old('kategoria_id') === (int) $category->id ? 'selected' : '' }}>
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
            <input id="cena" type="number" name="cena" min="0" step="0.01" value="{{ old('cena') }}" required>
            @error('cena')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="admin-form-field">
            <label for="skladom">Stock</label>
            <input id="skladom" type="number" name="skladom" min="0" step="1" value="{{ old('skladom') }}" required>
            @error('skladom')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div class="admin-form-field">
          <label for="popis">Description</label>
          <textarea id="popis" name="popis" required>{{ old('popis') }}</textarea>
          @error('popis')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="admin-form-field">
          <label for="images">Photos</label>
          <label class="admin-upload-box" id="admin-upload-box" for="images">
            <input id="images" type="file" name="images[]" accept="image/*" multiple required>
            <span class="admin-upload-icon">
              <i class="fa-regular fa-image"></i>
            </span>
            <span class="admin-upload-title">Upload photos</span>
            <small>Drag and drop images here, or click to choose files. Minimum 2 photos.</small>
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
          <input type="checkbox" name="aktivny" value="1" {{ old('aktivny', '1') ? 'checked' : '' }}>
          <span>Product is active and visible in the shop</span>
        </label>

        <div class="admin-form-actions">
          <a href="{{ route('admin.products.index') }}" class="cart-link-back-to-shop">Cancel</a>
          <button type="submit" class="btn">Create product</button>
        </div>
      </form>
    </section>
  </main>

  @include('admin.products.partials.upload-preview-script')
@endsection
