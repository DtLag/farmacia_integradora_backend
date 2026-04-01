<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = ['order_id', 'product_id', 'amount', 'unit_price', 'subtotal'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reservation()
    {
        return $this->hasOne(PickUpReservation::class, 'product_id', 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
