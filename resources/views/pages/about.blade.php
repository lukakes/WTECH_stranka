@extends('layouts.store')

@section('title', 'About - Sticker Shop')

@section('content')
  <main class="about-page container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> &gt; About
    </div>

    <section class="about-section">
      <h2>About us</h2>

      <div class="about-image">
        <img src="{{ asset('images/AboutUs/Team.jpg') }}" alt="Our team working together">
      </div>

      <div class="about-text">
        <p>Welcome to our little corner of cute and creative things!</p>

        <p>
          We're a small shop dedicated to bringing fun, personality, and a bit of joy
          to everyday life through stickers, enamel pins, and plushies. What started
          as a love for art, collecting, and adorable designs turned into a place
          where creativity can live on the things you carry, wear, and hug.
        </p>

        <p>
          Every product we create is designed with care and attention to detail.
          From expressive enamel pins that brighten up your jacket or bag, to durable
          stickers perfect for laptops, journals, and water bottles, to soft plushies
          made for comfort and companionship, everything in our shop is made to spark
          a smile.
        </p>

        <p>
          Our inspiration comes from cozy aesthetics, cute characters, internet culture,
          and the amazing creative community that supports small artists. We believe
          little things can make a big difference, and our goal is to create items that
          feel special, personal, and collectible.
        </p>

        <p>Thank you for supporting a small creative shop.</p>
      </div>
    </section>
  </main>
@endsection
