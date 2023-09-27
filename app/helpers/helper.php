<?php

namespace app\helpers;
use App\Models\Cart;


//helper function to calculate the total price of items in a user's cart
if(! function_exists('calculateTotalPrice')){

    function calculateTotalPrice(Cart $cart){

        $totalPrice = 0.00;

        $products = $cart -> products();

        foreach($products as $product){

            $quantity = $product->pivot->quantity;
            $price = $product->price;
            $subTotal = $quantity * $price;

            $totalPrice += $subTotal;
        }

        return $totalPrice;
    }
}



