<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('/pages/welcome');
});

Route::get('/home', function () {
    $featuredProducts = [
        [
            'name' => 'Cosmic Cat Sticker',
            'price' => 4.90,
            'image' => 'images/Products/prod-img-1.png',
            'href' => '#',
        ],
        [
            'name' => 'Sleepy Bean Sticker',
            'price' => 3.50,
            'image' => 'images/Products/prod-img-2.png',
            'href' => '#',
        ],
        [
            'name' => 'Pixel Paw Sticker',
            'price' => 5.20,
            'image' => 'images/Products/prod-img-3.png',
            'href' => '#',
        ],
        [
            'name' => 'Retro Kitty Sticker',
            'price' => 4.20,
            'image' => 'images/Products/prod-img-4.png',
            'href' => '#',
        ],
    ];

    return view('/pages/home', compact('featuredProducts'));
})->name('home');

Route::get('/register', function () {
    return view('pages.register');
})->name('register');