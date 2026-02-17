<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function reservations()
    {
        return $this->hasMany(PickUpReservation::class);
    }

    public function pendingSales()
    {
        return $this->saleDetails()->whereHas('sale', function ($query) {
            $query->where('state', 'in progress');
        })->exists();
    }

    public function reservedProduct()
    {
        return $this->reservations()->where('state', 'pending')->exists();
    }
}
