<?php

use Illuminate\Support\Facades\Route;

/*
* T&Cs and Privacy Policy
*/

Route::get('/terms-and-conditions', fn () => view('pages.terms-and-conditions'))->name('terms-and-conditions');
Route::get('/privacy-policy', fn () => view('pages.privacy-policy'))->name('privacy-policy');

/*
 * guest
 */
Route::middleware('guest')->group(function () {
});

/*
 * Authenticated
 */

Route::middleware('auth')->group(function () {
    Route::get('/home', fn () => view('pages.homepage'))->name('home');
});
