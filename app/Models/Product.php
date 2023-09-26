<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name' , 'description' , 'price' , 'quantity'];

    //The product can belongs to more than one cart(many to many realtionship)
    public function carts(){
        return $this->belongsToMany(Cart::class , 'cart_product' , 'cart_id' , 'product_id');
    }
}
