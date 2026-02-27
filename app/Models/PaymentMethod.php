<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'method_name',
        'slug'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'payment_method_id');
    }
}
