<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    use ApiResponse;

    public function store(SaleRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $subtotal = 0;
                $saleDetails = [];

                foreach ($request->products as $item) {
                    $product = Product::findOrFail($item['id']);

                    if ($product->stock < $item['amount']) {
                        throw new \Exception("Stock insuficiente para el producto: {$product->name}");
                    }

                    $itemSubtotal = $product->sale_price * $item['amount'];
                    $subtotal += $itemSubtotal;

                    $saleDetails[] = [
                        'product_id' => $product->id,
                        'amount' => $item['amount'],
                        'unit_price' => $product->sale_price,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                if($request->has('amount_received') && $request->amount_received < $subtotal) {
                    throw new \Exception("El monto recibido es insuficiente para cubrir el total de la venta.");
                }

                $sale = Sale::create([
                    'date_time' => now(),
                    'state' => 'completed',
                    'total' => $subtotal,
                    'subtotal' => $subtotal,
                    'payment_method_id' => $request->payment_method_id,
                    'user_id' => Auth::id(),
                    'customer_id' => $request->customer_id,
                ]);

                foreach ($saleDetails as $detail) {
                    $detail['sale_id'] = $sale->id;
                    SaleDetail::create($detail);

                    $product = Product::find($detail['product_id']);
                    $product->decrement('stock', $detail['amount']);

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'reason' => 'output',
                        'amount' => $detail['amount'],
                        'date_time' => now(),
                        'user_id' => Auth::id(),
                    ]);
                }

                return $this->response(
                    true,
                    'Venta procesada exitosamente',
                    [
                        'sale_id' => $sale->id,
                        'total' => $sale->total,
                        'change' => $request->amount_received ? ($request->amount_received - $sale->total) : 0,
                    ],
                    null,
                    201);
                });

        } catch (\Exception $e) {
            return $this->response(false, 'Error al procesar la venta: ' . $e->getMessage(), null, null, 400);
        }
    }
}
