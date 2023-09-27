<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id' , 'total_price'];

    protected $primaryKey = 'id';
    //The cart can have more than one product(many to many realtionship)
    public function products(){
        return $this->belongsToMany(Product::class , 'cart_product'  , 'id' ,'product_id')->withPivot('quantity');
    }

    //The cart belongs to only one user(one to one realtionship)
    public function user(){
        return $this->belongsTo(User::class);
    }

    //each cart has one oder(one to one relationship)
    public function order(){
        return $this->hasOne(Order::class);
    }


}
