<?php

namespace App\Traits;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function App\helpers\calculateTotalPrice;
trait Purchasable{
    use ApiResponse;
    public function purchase($quantity = 1)
    {
        // Check if the product is available
        if (!$this->isAvailable($quantity)) {
            return null;
        }

        // Start a database transaction
        DB::beginTransaction();

        try {

            $user = Auth::user();
            $cart = $user -> cart;
             // Calculate the total price of the cart
             $totalPrice = calculateTotalPrice($cart);

            // Create an order
            $order = Order::create([
                'user_id' => $user->id,
                'order_date'   => now() -> format('Y-m-d'),
                'total_amount' => $totalPrice

            ]);

            // Attach the product to the order
            $order->products()->attach($this->id, ['quantity' => $quantity]);

            // Update the product's inventory or any other necessary actions
            $this->updateInventory($quantity);

            // Commit the transaction
            DB::commit();

            return $order;
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            DB::rollBack();
            return null;
        }
    }

    public function isAvailable($quantity = 1)
    {
        return $this->product >= $quantity;
    }


    protected function updateInventory($quantity)
    {
        $this->product -= $quantity;
        $this->save();
    }
}


