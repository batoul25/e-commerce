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
        return $this->belongsToMany(Cart::class);
    }


    //The product can be associated with multiple orders(many to many realtioship)
    public function orders(){
        return $this->belongsToMany(Order::class);
    }
}
