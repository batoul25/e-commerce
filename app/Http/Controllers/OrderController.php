<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function app\helpers\calculateTotalPrice;

class OrderController extends Controller
{
    use ApiResponse;

    /**
     * Place an order for the products in the user's cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function placeOrder(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Retrieve the user's cart
        $cart = $user->cart;

        // Check if the cart is empty
        if (!$cart || $cart->products->isEmpty()) {
            return $this->errorResponse('The cart is empty', 400);
        }

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Create a new order

            $order = Order::create([
                'user_id'      => $user -> id ,
                'order_date'   => now() -> format('Y-m-d'),
                'total_amount' => calculateTotalPrice($cart) //use the helper to calculate the total price

            ]);

            $products = $cart -> products();
            // Attach products to the order
            foreach ($products as $product) {
                $quantity = $product->pivot->quantity;
                $price = $product->price;

                // Attach the product to the order with the quantity and price
                $order->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'price' => $price,
                ]);

                // Update the product's quantity in the inventory
                $product->quantity -= $quantity;
                $product->save();
            }

            // Clear the user's cart
            $cart->products()->detach();
            $cart->total_price = 0;
            $cart->save();

            // Commit the transaction
            DB::commit();

            return $this->successResponse($order, 'Order placed successfully', 201);
        } catch (\Exception $e) {
            // Rollback the transaction in case of any error
            DB::rollBack();

            return $this->errorResponse('Failed to place the order', 500);
        }
    }
}
