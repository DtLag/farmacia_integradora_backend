<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'register_date_time', 'scheduled_time', 'state', 'emproyee_id'];
    
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function reservations()
    {
        return $this->hasMany(PickUpReservation::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
