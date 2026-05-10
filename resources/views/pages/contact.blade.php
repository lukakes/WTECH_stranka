@extends('layouts.store')

@section('title', 'Contact - Sticker Shop')

@section('content')
  <main class="about-page container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> &gt; Contact
    </div>

    <section class="about-section">
      <h2>Contact us</h2>

      <form class="contact-form" method="GET" action="{{ route('contact') }}">
        <input type="text" name="name" placeholder="Name">
        <input type="email" name="email" placeholder="Email">
        <input type="text" name="phone" placeholder="Phone">
        <textarea name="message" placeholder="Message"></textarea>
        <button class="btn" type="submit">Send</button>
      </form>
    </section>
  </main>
@endsection
