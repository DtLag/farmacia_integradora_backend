<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'name',
        'presentation',
        'purchase_price',
        'sale_price',
        'location',
        'stock',
        'min_stock',
        'max_stock',
        'description',
        'image',
        'supplier_id',
        'category_id'
    ];
    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier() 
    {
        return $this->belongsTo(Supplier::class);
    }

    public function productReceptions()
    {
        return $this->hasMany(ProductReception::class);
    }
}
