<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePickUpRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\PickUpReservation;
use App\Models\Audit;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PickUpController extends Controller
{
    use ApiResponse;

    public function store(CreatePickUpRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $customer = Auth::user()->customer;

            if (!$customer) {
                return $this->response(false, 'No autenticado', null, null, 401);
            }

            $scheduled = Carbon::parse($request->scheduled_time);

            if ($scheduled->isPast() || $scheduled->isTomorrow()) {
                return $this->response(false, 'Hora no válida. Debe ser hoy', null, 'Hora invalida', 422);
            }

            $order = Order::create([
                'customer_id' => $customer->id,
                'scheduled_time' => $scheduled,
                'state' => 'pending'
            ]);

            foreach ($request->products as $prod) {

                $product = Product::find($prod['product_id']);

                if ($product->stock < $prod['amount']) {

                    return $this->response(false, "Stock insuficiente para {$product->name}", null, 'Stock insuficiente', 409
                    );
                }

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'amount' => $prod['amount'],
                    'unit_price' => $product->sale_price,
                    'subtotal' => $product->sale_price * $prod['amount']
                ]);

                PickUpReservation::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'amount' => $prod['amount'],
                    'state' => 'pending'
                ]);

                $product->stock = $product->stock - $prod['amount'];
                $product->save();
            }

            Audit::create([
                'user_id' => Auth::id(),
                'affected_module' => 'orders',
                'action_performed' => 'create',
                'detail' => "Pedido PickUp creado ID {$order->id}"
            ]);

            return $this->response(true, 'Pedido creado correctamente', $order, null, 201);
        });
    }
}
