<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    use ApiResponse;

    //productos con stock bajo
    public function lowStock(){
        $stock = Product::whereColumn('stock', '<=', 'min_stock')->get();

        return $this->response(true, 'Productos con stock bajo', $stock, null, 201);
    }

    //productos próximos a vencer
    public function expireSoon()
    {
        $product = Product::whereHas('productReceptions', function($q){
            $q->whereBetween('expiration_date', [now(), now()->addDays(7)]);
        })->with(['productReceptions' => function($q) {
            $q->whereBetween('expiration_date', [now(), now()->addDays(7)]);
        }])->get();
        return $this->response(true, 'Productos que están por caducar', $product, null, 201);
    }

    //productos caducados
    public function expired()
    {
        $product = Product::whereHas('productReceptions', function($q){
            $q->where('expiration_date', '<' ,now());
        })->with(['productReceptions' => function($q) {
            $q->where('expiration_date', '<', now());
        }])->get();
        return $this->response(true, 'Productos caducados', $product, null, 201);
    }
}
