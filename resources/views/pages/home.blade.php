@extends('layouts.store')

@section('title', 'Home - Sticker Shop')

@section('content')

  <section class="hero-area">
    <section class="hero">
      <div class="hero-popup">
        <h1>Welcome</h1>
        <p>Don't wait around for other people to grab your favorite pieces!</p>
        <a href="#featured-products" class="btn">Shop now</a>
      </div>
      <div class="hero-grid">
        <img src="{{ asset('images/Homepage/cats/cat1.png') }}" alt="cat sticker">
        <img src="{{ asset('images/Homepage/cats/cat2.png') }}" alt="cat sticker">
        <img src="{{ asset('images/Homepage/cats/cat3.png') }}" alt="cat sticker">
        <img src="{{ asset('images/Homepage/cats/cat4.png') }}" alt="cat sticker">
        <img src="{{ asset('images/Homepage/cats/cat5.png') }}" alt="cat sticker">
        <img src="{{ asset('images/Homepage/cats/cat6.png') }}" alt="cat sticker">
      </div>
    </section>
  </section>

  <section class="featured-section" id="featured-products">
    <h2>Featured</h2>
    <div class="featured-grid container">
      <a href="{{ route('home') }}#featured-products" class="product-card">
        <div class="product-image">
          <img src="{{ asset('images/Products/prod-img-1.png') }}" alt="Jotaro sticker">
        </div>
        <div class="product-info">
          <p>Jotaro sticker</p>
          <p>3.90 EUR</p>
        </div>
      </a>

      <a href="{{ route('home') }}#featured-products" class="product-card">
        <div class="product-image">
          <img src="{{ asset('images/Products/prod-img-2.png') }}" alt="Pikachu pin">
        </div>
        <div class="product-info">
          <p>Pikachu pin</p>
          <p>4.90 EUR</p>
        </div>
      </a>

      <a href="{{ route('home') }}#featured-products" class="product-card">
        <div class="product-image">
          <img src="{{ asset('images/Products/prod-img-3.png') }}" alt="Cat plush">
        </div>
        <div class="product-info">
          <p>Cat plush</p>
          <p>14.90 EUR</p>
        </div>
      </a>

      <a href="{{ route('home') }}#featured-products" class="product-card">
        <div class="product-image">
          <img src="{{ asset('images/Products/prod-img-4.png') }}" alt="Game over pin">
        </div>
        <div class="product-info">
          <p>Game over pin</p>
          <p>3.90 EUR</p>
        </div>
      </a>
    </div>
  </section>
  <section class="promo-section">
      <div class="promo-content container">
        <div class="promo-text">
          <h2>Rest in peace my granny she got hit by a bazooka</h2>
          <p>
            Samozrejme ze viem aky placeholder text tu mam napisat,
            aby to tematicky sedelo ku zvysku stranky
          </p>
          <a href="{{ route('home') }}#about-shop" class="btn">See more !</a>
        </div>

        <div class="promo-image">
          <img src="{{ asset('images/Homepage/Squidward plush.png') }}" alt="Featured plush image">
        </div>
      </div>
    </section>

    <section class="promo-section">
      <div class="promo-content reverse container">
        <div class="promo-text">
          <h2>おばあちゃん、安らかに眠ってください。</h2>
          <p>
            Ešte mám z ich vystúpenia lístok<br>
            Páry sa mi zdali príliš blízko<br>
            Počkal som si kým sa odkrojí<br>
            Ešte krajšia bude v bielom závoji<br>
            Potom prišli ďakí veľkí chlapi<br>
            Spýtali sa, či ma niečo trápi
          </p>
          <a href="{{ route('home') }}#contact-shop" class="btn">See more !</a>
        </div>
        
        <div class="promo-image">
          <img src="{{ asset('images/Homepage/Demon Slayer pins.png') }}" alt="demonslayer pins">
        </div>
      </div>
    </section>
    <section class="bottom-space" id="about-shop">
        <div class="bottom-text-row ">
          <div class="bottom-text-left-container">
            <h2>Here text</h2>
            <p>I'm standing at the door of the club<br>
                Breath smelling like a pub<br>  
                Gettin VIP love cause the people know my name<br>
                There's cocaine running around in my brain<br>
                So I chat to everybody, the cocaine's to blame<br>
                This chick that I'm with is a dime, she looks flame<br>
                But I really don't remember her name, so hey ho!<br>
              </p>
          </div>
          <div class="bottom-text-center-container">
            <h2>Here text</h2>
            <p>I'm standing at the door of the club<br>
                Breath smelling like a pub<br>  
                Gettin VIP love cause the people know my name<br>
                There's cocaine running around in my brain<br>
                So I chat to everybody, the cocaine's to blame<br>
                This chick that I'm with is a dime, she looks flame<br>
                But I really don't remember her name, so hey ho!<br>
            </p>
          </div>
          <div class="bottom-text-right-container">
            <h2>Here text</h2>
            <p>I'm standing at the door of the club<br>
                Breath smelling like a pub<br>  
                Gettin VIP love cause the people know my name<br>
                There's cocaine running around in my brain<br>
                So I chat to everybody, the cocaine's to blame<br>
                This chick that I'm with is a dime, she looks flame<br>
                But I really don't remember her name, so hey ho!<br>
            </p>
          </div>
        </div>
    </section>

@endsection