<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\PickUpReservation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CancelPickUpOrderController extends Controller
{
    use ApiResponse;
    
    public function automaticCancel()
    {
        $orders = Order::where('state', 'pending')->where('scheduled_time', '<', now())->get();

        foreach($orders as $order){
            DB::transaction( function () use($order) {
                if($order->state !== 'pending'){
                    return;
                }
            
                $reservations = PickUpReservation::where('order_id', $order->id)->get();

                foreach($reservations as $reservation){
                    $product = $reservation->product;
                    $product->stock += $reservation->amount;
                    $product->save();
                
                    $reservation->state = 'canceled';
                    $reservation->save();

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'reason' => 'income',
                        'amount' => $reservation->amount,
                        'date_time' => now(),
                        'user_id' => null
                    ]);
                }
            
                $order->state = 'canceled';            
                $order->save();
            
                return $this->response(true, 'Pedido cancelado', $order, null, 200);
            });
        }
    }

    public function manualCancel($orderId)
    {
        return DB::transaction( function () use ($orderId) {
            $order = Order::findOrFail($orderId);

            if($order->state !== 'pending'){
                return $this->response(false, 'El pedido ya fue procesado o cancelado', null, null, 409);
            }

            $reservations = PickUpReservation::where('order_id', $order->id)->get();

            foreach($reservations as $reservation){
                $product = $reservation->product;

                $product->stock += $reservation->amount;
                $product->save();

                $reservation->state = 'canceled';
                $reservation->save();

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'reason' => 'income',
                    'amount' => $reservation->amount,
                    'date_time' => now(),
                    'user_id' => Auth::id()
                ]);
            }

            $order->state = 'canceled';
            $order->save();

            return $this->response(true, 'Pedido cancelado', $order, null, 200);

        });
    }
}
