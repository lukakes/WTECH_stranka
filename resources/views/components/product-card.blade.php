@php
  $name = data_get($product, 'name', 'Placeholder sticker');
  $price = data_get($product, 'price', 0);
  $image = data_get($product, 'image', 'images/Products/prod-img-1.png');
  $href = data_get($product, 'href', '#');
@endphp

<a href="{{ $href }}" class="product-card">
  <div class="product-image">
    <img src="{{ asset($image) }}" alt="{{ $name }}">
  </div>
  <div class="product-info">
    <p>{{ $name }}</p>
    <p>{{ number_format((float) $price, 2, '.', '') }} €</p>
  </div>
</a>