<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id' , 'order_date' , 'total_amount'];

    //each order belongs to one user(one to many realtionship)
    public function user(){
        $this->belongsTo(User::class);
    }

    //The order can contain multiple products(many to many realtionship)
    public function products(){
        return $this->belongsToMany(Product::class);
    }
}
