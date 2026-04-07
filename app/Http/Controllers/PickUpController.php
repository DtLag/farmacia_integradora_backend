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
        try {
            $order = DB::transaction(function () use ($request) {

                $customer = Auth::user()->customer;

                if (!$customer) {
                    throw new Exception('No autenticado', 401);
                }

                $scheduled = Carbon::parse($request->scheduled_time);

                if ($scheduled->isPast() || $scheduled->isTomorrow()) {
                    throw new Exception('Hora no válida. Debe ser hoy', 422);
                }

                $order = Order::create([
                    'customer_id' => $customer->id,
                    'scheduled_time' => $scheduled,
                    'state' => 'pending'
                ]);

                foreach ($request->products as $prod) {
                    $product = Product::findOrFail($prod['product_id']);

                    if ($product->stock < $prod['amount']) {
                        throw new Exception("Stock insuficiente para {$product->name}", 409);
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

                return $order; 
            });

            $order->load('customer');
            broadcast(new NewPickUpOrderReceived($order));

            return $this->response(true, 'Pedido creado correctamente', $order, null, 201);

        } catch (Exception $e) {
            $code = $e->getCode() ?: 400; 
            return $this->response(false, $e->getMessage(), null, 'Error en pedido', $code);
        }
    }

    public function index(string $state)
    {
        $orders = Order::with(['orderDetails.product', 'customer', 'employee', 'payment'])->where('state', $state)->get();

        return $this->response(true, "Pedidos", $orders, null, 200);
    }
}