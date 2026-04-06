<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'register_date_time', 'scheduled_time', 'state', 'payment_method_id', 'employee_id'];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function payment()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
