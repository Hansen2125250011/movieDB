<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('movies.index');
    }
    return redirect()->route('login');
});

Auth::routes();

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'id'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    $previous = url()->previous();
    if ($previous && parse_url($previous, PHP_URL_HOST) === request()->getHost()) {
        return redirect($previous);
    }
    return redirect()->route('movies.index');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
    Route::get('/movies/{imdb}', [MovieController::class, 'show'])->name('movies.show');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{imdb}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});

Route::get('/home', function () {
    return redirect()->route('movies.index');
})->name('home')->middleware('auth');