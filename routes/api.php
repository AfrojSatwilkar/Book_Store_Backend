<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api'], function() {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('logout', [UserController::class, 'logout']);

    Route::post('forgotpassword', [ForgotPasswordController::class, 'forgotPassword']);
    Route::post('resetpassword', [ForgotPasswordController::class, 'resetPassword']);

    Route::post('addbook', [BookController::class, 'addBook']);
    Route::post('updatebook', [BookController::class, 'updateBookByBookId']);
    Route::post('addquantity', [BookController::class, 'addQuantityToExistingBook']);
    Route::post('deletebook', [BookController::class, 'deleteBookByBookId']);
    Route::get('displaybooks', [BookController::class, 'getAllBooks']);
    Route::get('sortlowtohigh', [BookController::class, 'sortOnPriceLowToHigh']);
    Route::get('sorthightolow', [BookController::class, 'sortOnPriceHighToLow']);
    Route::post('searchbook', [BookController::class, 'searchByEnteredKeyWord']);

    Route::post('addtocart', [CartController::class, 'addBookToCartByBookId']);
    Route::post('deletecart', [CartController::class, 'deleteBookByCartId']);
    Route::get('getcart', [CartController::class, 'getAllBooksByUserId']);
    Route::post('increamentquantity', [CartController::class, 'increamentBookQuantityInCart']);
    Route::post('decreamentquantity', [CartController::class, 'decreamentBookQuantityInCart']);
    Route::post('addtocartbywishlistid', [CartController::class, 'addBookToCartByWishlistId']);

    Route::post('addaddress', [AddressController::class, 'addAddress']);
    Route::post('updateaddress', [AddressController::class, 'updateAddress']);
    Route::post('deleteaddress', [AddressController::class, 'deleteAddress']);
    Route::get('getaddress', [AddressController::class, 'getAddress']);

    Route::post('addtowishlist', [WishlistController::class, 'addBookToWishlistByBookId']);
    Route::post('deletewishlist', [WishlistController::class, 'deleteBookByWishlistId']);
    Route::get('getwishlist', [WishlistController::class, 'getAllBooksInWishlist']);

    Route::post('placeorder', [OrderController::class, 'placeOrder']);
});
