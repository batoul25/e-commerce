<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\Purchasable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function App\helpers\calculateTotalPrice;

class OrderController extends Controller
{
    use ApiResponse;
    use Purchasable;

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
        if (!$cart || $cart->products->isEmpty() ){
            return $this->errorResponse('The cart is empty', 400);
        }

        // Begin a database transaction
        DB::beginTransaction();

        try {

            // Calculate the total price of the cart
            $totalPrice = calculateTotalPrice($cart);

            // Create a new order

            $order = Order::create([
                'user_id'      => $user -> id ,
                'order_date'   => now() -> format('Y-m-d'),
                'total_amount' => $totalPrice

            ]);


            $products = $cart->products;


            // Attach products to the order
            foreach ($products as $product){
                $quantity = $product->pivot->quantity;

                // Purchase the product using the Purchasable trait
                $order = $product->purchase($quantity);

                // if (!$order) {
                //     throw new \Exception('Failed to place the order for product ' . $product->id);
                // }


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

           // return $this->errorResponse('Failed to place the order', 500);
          return $this->errorResponse('Failed to place the order: ' . $e->getMessage(), 500);
        }
    }
}
