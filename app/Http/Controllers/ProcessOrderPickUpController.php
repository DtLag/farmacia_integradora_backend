<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Audit;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\PickUpStatusUpdated;

class ProcessOrderPickUpController extends Controller
{
    use ApiResponse;

    public function indexPending()
    {
        $orders = Order::where('state', 'pending')
            ->with('orderDetails.product')
            ->get();

        return $this->response(true, 'Pedidos pendientes', $orders, null, 200);

        
    }


    public function startPreparation($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->response(false, 'Pedido no encontrado', null, null, 404);
        }

        $order->state = 'preparing';
        $order->save();

        broadcast(new PickUpStatusUpdated($order));

        return $this->response(true, 'Pedido en preparación', $order, null, 200);
    }


    public function finishPreparation($id)
    {
        return DB::transaction(function () use ($id) {

            $order = Order::find($id);

            if (!$order) {
                return $this->response(false, 'Pedido no encontrado', null, null, 404);
            }

            if ($order->state !== 'preparing') {
                return $this->response(false, 'El pedido no está en preparación', null, null, 409);
            }

            $order->state = 'ready';
            $order->save();

            broadcast(new PickUpStatusUpdated($order));

            return $this->response(
                true,
                'Pedido listo para recoger',
                $order,
                null,
                200
            );
        });
    }
}
