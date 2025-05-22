<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
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
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => ['auth', 'verified', 'role:Admin|Petugas']], function () {
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    // Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    // Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    // Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::resource('/books', BookController::class)->except(['create', 'show', 'store']);
});

Route::middleware(['auth', 'role:Admin|Petugas'])->group(function () {
    Route::middleware(['auth', 'role:Admin'])->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::resource('permissions', PermissionController::class);
        Route::put('/permissions/{permission}/users', [PermissionController::class, 'updateUsers'])->name('permissions.updateUsers');
        Route::put('/permissions/{permission}/roles', [PermissionController::class, 'updateRoles'])->name('permissions.updateRoles');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.updateRoles');
    Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.updatePermissions');
    Route::get('/activity-logs/export-excel', [ActivityLogController::class, 'exportExcel'])
        ->name('activity-logs.export-excel');
    
    Route::get('/activity-logs/export-pdf', [ActivityLogController::class, 'exportPDF'])
        ->name('activity-logs.export-pdf');
});




require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
