<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
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

//
Route::controller(AuthController::class)->group(function () {
    //Route for User login and JWT token generation
    Route::post('login', 'login');
    //Route for  User registration
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

//Routes for the product
Route::controller(ProductController::class)->group(function(){

    //Route for Retrieve a list of available products
    Route::get('/products' , 'index') ;
    //Route for create a product (only the admin can create a new product)
    Route::post('/products' ,  'store') -> middleware(['roles:admin']);
    //Route for Retrieve a specific product using the id
    Route::get('/products/{id}' , 'show');
    //Route for Update a specific product using the id (only the admin can update the data of the product)
    Route::post('/products/{id}' , 'update') -> middleware(['roles:admin']);
    //Route for Delete specific product using the id (only the admin can delete a product)
    Route::post('/products/{id}' , 'destroy') -> middleware(['roles:admin']);

})->middleware(['auth:api']);

//Retrieve the user's shopping cart
Route::get('/cart' ,[CartController::class , 'index'])->middleware('auth:api');

//Add a product to the cart
Route::post('/cart/add/{product_id}' , [CartController::class , 'addToCart'])->middleware('auth:api');

//Remove a product from the cart
Route::post('/cart/remove/{product_id}' , [CartController::class , 'removeFromCart'])->middleware('auth:api');

//Place an order for items in the cart
Route::post('/orders/place' , [OrderController::class , 'placeOrder'])->middleware('auth:api');

