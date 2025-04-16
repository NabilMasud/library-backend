<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', 'role:Admin|Petugas'])->name('dashboard');

Route::group(['middleware' => ['auth', 'verified', 'role:Admin|Petugas']], function () {
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    // Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    // Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    // Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::resource('/books', BookController::class)->except(['create', 'show', 'store']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
