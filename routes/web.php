<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "web" middleware group. Make something great!
|
*/

// Home route
Route::get('/', [AppController::class, 'index'])->name('app.index');
Route::get('/shop',[ShopController::class,'index'])->name('shop.index');
Route::get('/product{slug}',[ShopController::class,'productDetails'])->name('shop.product.details');

Route::get('/cart',[CartController::class, 'index'])->name('cart.index');
Route::post('/cart/store',[CartController::class, 'addToCart'])->name('cart.store');
Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::delete('/cart/remove',[CartController::class, 'removeitem'])->name('cart.remove');
Route::delete('/cart/clear',[CartController::class, 'clearCart'])->name('cart.clear');
// Authentication routes
Auth::routes(); // Ini sudah mencakup login, register, dan logout

// User routes
Route::middleware('auth')->group(function () {
    Route::get('/my-account', [UserController::class, 'index'])->name('user.index');
});

// Admin routes
Route::middleware(['auth', 'auth.admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
