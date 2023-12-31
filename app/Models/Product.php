<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Purchasable;

class Product extends Model
{
    use HasFactory, Purchasable;

    protected $fillable = ['name', 'description', 'price', 'quantity'];

    // The product can belong to more than one cart (many-to-many relationship)
    public function carts()
    {
        return $this->belongsToMany(Cart::class);
    }

    // The product can be associated with multiple orders (many-to-many relationship)
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
