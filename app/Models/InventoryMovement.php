<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id',
        'reason',
        'amount',
        'data_time',
        'user_id',
    ];
}
