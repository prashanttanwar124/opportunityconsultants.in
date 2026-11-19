<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/free-counselling', [LeadController::class, 'create'])->name('apply');
Route::post('/free-counselling', [LeadController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('apply.store');

Route::post('/callback', [LeadController::class, 'storeCallback'])
    ->middleware('throttle:10,1')
    ->name('callback.store');

// Custom CMS pages created from the admin panel (never shadow built-in/system routes)
Route::get('/{page:slug}', [PageController::class, 'show'])
    ->where('page', '^(?!admin(?:/|$)|blog(?:/|$)|livewire(?:/|$)|storage(?:/|$)|up(?:/|$)|register(?:/|$))[a-z0-9-]+$')
    ->name('pages.show');
