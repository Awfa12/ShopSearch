<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Livewire\ProductSearch;

Route::get('/', ProductSearch::class)->name('home');
Route::get('/search', [SearchController::class, 'search'])->name('search');
