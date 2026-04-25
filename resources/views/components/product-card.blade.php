<a href="{{ $href }}" class="product-card">
  <div class="product-image">
    <img src="{{ asset($image) }}" alt="{{ $name }}">
  </div>
  <div class="product-info">
    <p>{{ $name }}</p>
    <p>{{ number_format((float) $price, 2, '.', '') }} €</p>
  </div>
</a>