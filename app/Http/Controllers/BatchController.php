<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReceptionRequest;
use App\Models\Batch;
use App\Models\ProductReception;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ReceptionResource;
use App\Traits\ApiResponse;

class BatchController extends Controller
{
    use ApiResponse;
    /**
     * CU-02: Registrar Recepción de Lote
     */                                     // 1. Validación        
    public function registerBatchReception(ReceptionRequest $request)
    {
        

        try {
            DB::beginTransaction();

            $totalUnits = collect($request['products'])->sum('amount');

            // Calcular total monetario
            $totalMoney = collect($request['products'])->sum(function ($item) {
                return $item['amount'] * $item['unit_price'];
            });

            // 1. Crear el Lote (Cabecera)
            $batch = Batch::create([
                'identifier_batch' => $request['identifier_batch'],
                'supplier_id' => $request['supplier_id'] ?? null,
                'entry_date' => $request['entry_date'] ?? null,
                'notes' => $request['notes'] ?? null,
                'products_count' => count($request['products']),
                'units_count' => $totalUnits,
                'total' => $totalMoney,
            ]);

            // 2. Registrar cada producto del lote
            foreach ($request['products'] as $productData) {
                // Registrar recepción
                ProductReception::create([
                    'product_id' => $productData['product_id'],
                    'amount' => $productData['amount'],
                    'batch_id' => $batch->id,
                    'user_id' => auth()->id(),
                    'unit_price' => $productData['unit_price'],
                    'expiration_date' => $productData['expiration_date'] ?? null 
                ]);

                // Actualizar stock global del producto
                $product = Product::findOrFail($productData['product_id']);
                $product->stock += $productData['amount'];
                $product->save();
            }

            DB::commit();

            return 
                $this->response(true, 'Batch reception registered successfully', new ReceptionResource($batch),null, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response(false, 'Error registering batch ',null, $e->getMessage(), 500);
        }
    }


}
