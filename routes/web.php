<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::resource('posts', PostController::class);
Route::resource('authors', AuthorController::class);

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth','verified'])->group(function(){
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::post('/add-comment/{post}', [CommentController::class, 'store'])->name('comments.add');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('markers', [MarkerController::class, 'index'])->name('markers.index');
    Route::post('markers', [MarkerController::class, 'store'])->name('markers.store');
    Route::put('markers/{marker}', [MarkerController::class, 'update'])->name('markers.update');
    Route::delete('markers/{marker}', [MarkerController::class, 'destroy'])->name('markers.destroy');
});




require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/posts.php';
