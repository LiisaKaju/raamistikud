<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HikingSubjectController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::resource('authors', AuthorController::class);
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::post('/cart/add/{product}', [ShopController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/update/{product}', [ShopController::class, 'updateCart'])->name('cart.update');
Route::delete('/cart/remove/{product}', [ShopController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
Route::post('/checkout/start', [ShopController::class, 'startPayment'])->name('shop.payment.start');
Route::get('/checkout/result', [ShopController::class, 'paymentResult'])->name('shop.result');

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('hiking', [HikingSubjectController::class, 'index'])->name('hiking.index');
    Route::post('hiking', [HikingSubjectController::class, 'store'])->name('hiking.store');
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
