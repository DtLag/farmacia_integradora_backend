<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReception extends Model
{
    use SoftDeletes;

    protected $table = 'product_receptions';
    
    protected $fillable = [
    'batch_id',
    'product_id',
    'amount',
    'unit_price',
    'reception_date',
    'user_id',
    'expiration_date',
];

    public $timestamps = true;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}


