<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'identifier_batch',
        'supplier_id',
        'entry_date',
        'notes',
        'products_count',
        'units_count',
        'total',
    ];
    protected $dates = [
        'entry_date',
    ];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function productReceptions()
    {
        return $this->hasMany(ProductReception::class);
    }
    public function product()
{
    return $this->hasManyThrough(Product::class, ProductReception::class, 'batch_id', 'id', 'id', 'product_id')->withTrashed();
}

}
