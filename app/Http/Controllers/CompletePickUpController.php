<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\PickUpReservation;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompletePickUpController extends Controller
{
    use ApiResponse;

    public function completeOrder(Request $request, int $orderId, int $customerId)
    {
        return DB::transaction(function () use($request, $orderId, $customerId) {

            $order = Order::with('orderDetails.product')->findOrFail($orderId);

            if($order->state !== 'ready'){
                return $this->response(false, 'El pedido no está listo para recoger', null, null, 409);
            }

            $customer = Customer::findOrFail($customerId);

            if($order->customer_id !== $customer->id){
                return $this->response(false, 'El cliente no coincíde', null, null, 409);
            }
        
        

            $sale = Sale::create([
                'date_time' => now(),
                'state' => 'completed',
                'total' => 0,
                'subtotal' => 0,
                'payment_method_id' => $request->payment_method_id,
                'customer_id' => $order->customer_id
            ]);

            $total = 0;

            foreach($order->orderDetails as $prods){

                $subtotal = $prods->unit_price * $prods->amount;

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $prods->product_id,
                    'amount' => $prods->amount,
                    'unit_price' => $prods->unit_price,
                    'subtotal' => $subtotal
                ]);

                $total += $subtotal;

                $product = $prods->product;

                if(!$product){
                    return $this->response(false, 'Producto no encontrado', null, null, 404);
                }

                if ($product->stock < $prods->amount) {
                    return $this->response(false, 'Stock insuficiente', null, null, 409);;
                }
    
                $product->stock -= $prods->amount;    
                $product->save();

            }

            $sale->subtotal = $total;
            $sale->total = $total;
            $sale->save();

            PickUpReservation::where('order_id', $order->id)->update(['state' => 'completed']);

            $order->state = 'completed';
            $order->payment_method_id = $request->payment_method_id;
            $order->save();

            return $this->response(true, 'Pedido completado', [
                'sale' => $sale
            ], null, 200);
            
        });
    }
}
