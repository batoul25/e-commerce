<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\helpers;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function app\helpers\calculateTotalPrice;

class CartController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Retrieve the cart for the user
        $cart = $user->cart;

        $products = [];


        // Iterate over the products in the cart to bring additional info about products such as quantity
        foreach ($cart->products as $product) {
            $quantity = $product->pivot->quantity;


            $additionalData = [
                'quantity' => $quantity,

            ];

            // Return every product in the cart with its quantity and subtotal
            $productData = [
                'product' => $product->toArray(),
                'additional_data' => $additionalData
            ];

            $products[] = $productData;
            //use the helper function to calulate the toatl price of the products in the cart
            $totalPrice = calculateTotalPrice($cart);
        }

        // Update the total_price column in the cart table
        $cart->total_price = $totalPrice;
        $cart->save();

        return $this->successResponse([
            'products' => $products,
            'total_price' => $totalPrice
        ]);
    }

    //Method to add products to the cart
    public function addToCart(Request $request,$productId){
        $user = Auth::user();
        $cart = $user -> cart;
        //if the user doesn't have a cart create a new cart
        if(!$cart){
            $cart = new Cart();
            $user->cart()->save($cart);
        }

        $product = Product::find($productId);
        //check if the product is found or not
        if(!$product){
            return $this->errorResponse('The product is not found' , 404);
        }
        //check if the quantity in the inventory is enough or not
        if($request->quantity > $product->quantity){
            return $this->errorResponse('The Quanity in the inventory is not enough' , 400);
        }
        //check if the user add an exisiting product to the cart
        $exisitingProduct = $cart->products()->find($product->id);
        //if the user added an exisiting product then we change the quantity
        if($exisitingProduct){
            $cart->products()->updateExistingPivot($product->id ,
                    ['quantity' => $exisitingProduct->pivot->quantity + $request->quantity]);

            // Update the quantity in the Product table
            $product->quantity -= $request->quantity;
            $product->save();
        }else{
            //if the user added a new product then we update the quantity of the product
            $cart->products()->attach($product->id, ['quantity'=>$request->quantity]);
            // Update the quantity in the Product table
            $product->quantity -= $request->quantity;
            $product->save();
        }


        return $this->successResponse(null , 'The Product added successfully to the cart' , 200);

    }

    //method to remove a product from the cart
    public function removeFromCart(Request $request, $productId)
    {
        $user = Auth::user();
        $cart = $user->cart;

        $product = Product::find($productId);
        if (!$product) {
            return $this->errorResponse('The Product is not found', 404);
        }


        $existingProduct = $cart->products()->find($product->id);
        //check if the product is found in the cart or not
        if (!$existingProduct) {
            return $this->errorResponse('The Product is not found in the cart', 404);
        } else {
            // Calculate the quantity to remove from the cart
            $quantityToRemove = $request->quantity;

            // Check if the requested quantity is greater than the quantity in the cart
            if ($quantityToRemove > $existingProduct->pivot->quantity) {
                return $this->errorResponse('The requested quantity exceeds the quantity in the cart', 400);
            }

            // Update the quantity in the cart's pivot table
            $newQuantity = $existingProduct->pivot->quantity - $quantityToRemove;
            if($newQuantity == 0){
                $cart->detach($product->id);
            }else{
            $cart->products()->updateExistingPivot($product->id, ['quantity' => $newQuantity]);
            }
            // Update the quantity in the Product table
            $product->quantity += $quantityToRemove;
            $product->save();

            // Calculate the price to subtract from the cart's total price
            $removedPrice = $product->price * $quantityToRemove;

            // Update the total price in the cart
            $cart->total_price -= $removedPrice;
            $cart->save();
        }

        return $this->successResponse(null, 'The Product was removed from the cart successfully', 201);
    }

}
