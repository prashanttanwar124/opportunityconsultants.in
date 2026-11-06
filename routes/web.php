<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/free-counselling', [LeadController::class, 'create'])->name('apply');
Route::post('/free-counselling', [LeadController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('apply.store');

Route::post('/callback', [LeadController::class, 'storeCallback'])
    ->middleware('throttle:10,1')
    ->name('callback.store');
