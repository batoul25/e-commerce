<?php

namespace App\helpers;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;


//helper function to calculate the total price of items in a user's cart
if(! function_exists('calculateTotalPrice')){

    function calculateTotalPrice(Cart $cart){

        $totalPrice = 0.00;
        // $user = Auth::user();
        // $cart = $user->cart;
        // $cart = find($cart -> id);

        $products = $cart->products()->get();

        foreach($products as $product){

            $quantity = $product->pivot->quantity;
            $price = $product->price;
            $subTotal = $quantity * $price;

            $totalPrice += $subTotal;
        }

        return $totalPrice;
    }
}



