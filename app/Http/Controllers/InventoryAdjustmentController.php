<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

use function Laravel\Prompts\error;

class InventoryAdjustmentController extends Controller
{
    use ApiResponse;

    public function alter(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $productStock = $product->stock;

        if($request->reason === 'income'){
            $newAdjustment = $productStock + $request->amount;
        }
        
        if($request->reason === 'output'){
            if($request->amount > $productStock){
                return $this->response(false, "Stock insuficiente", null, "El stock es menor a la cantidad", 422);
            }
            $newAdjustment = $productStock - $request->amount;
        }
        
        if($request->reason === 'adjustment'){
            $newAdjustment = $request->amount;
        }

        $product->stock = $newAdjustment;
        $product->save();

        InventoryMovement::create([
            'product_id' => $product->id,
            'reason' => $request->reason,
            'amount' => $request->amount,
            'date_time' => $request->date_time,
            'user_id' => auth()->id
        ]);

        return $this->response(true, "Hubo un cambio en el inventario y stock del producto", $product, null, 200);
    }
}
