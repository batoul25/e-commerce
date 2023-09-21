<?php

use App\Http\Controllers\AuthController;
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


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

//Routes for the product
Route::controller(ProductController::class)->group(function(){

    Route::get('/products' , 'index') ;
    Route::post('/products' ,  'store') -> middleware(['roles:admin']);
    Route::get('/products/{id}' , 'show');
    Route::post('/products/{id}' , 'update') -> middleware(['roles:admin']);
    Route::post('/products/{id}' , 'destroy') -> middleware(['roles:admin']);

})->middleware(['auth:api']);


