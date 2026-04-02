<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    use ApiResponse;

    public function delete(int $id)
    {
        $product = Product::findOrFail($id);

        if($product->pendingSales()){
            return $this->response(
                false, "El producto está en una venta pendiente, no se puede eliminar", null, 1, 409
            );
        }

        if($product->reservedProduct()){
            return $this->response(
                false, "El producto está reservado, no se puede eliminar", null, 1, 409
            );
        }

        $product->delete();

        return $this->response(
            true, "El producto se ha eliminado", null, null, 201
        );
    }
}
