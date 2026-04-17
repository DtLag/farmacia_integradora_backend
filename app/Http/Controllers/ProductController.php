<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Traits\ApiResponse;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProductResource;
use App\Http\Requests\UpdateReceptionRequest;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    use ApiResponse;


    /**
     * CU-01: Registrar Producto (Crear)
     */                             
    public function registerProduct(ProductRequest $request)
    {
        $data = $request->except('image');

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('product_images', 'public');
            }
            
            $product = Product::create($data);

            DB::commit();

            return $this->response(true, 'Producto registrado exitosamente', new ProductResource($product), null, 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error General (Registro): " . $e->getMessage());
            return $this->response(false, 'Error al registrar producto: ' . $e->getMessage(), null, null, 500);
        }
    }

    /**
     * CU-03: Editar Producto (Update)
     */                    
    public function update(UpdateReceptionRequest $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $data = $request->except('image');

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $data['image'] = $request->file('image')->store('product_images', 'public');
            }

            $product->update($data);

            DB::commit();
            return $this->response(true, 'Producto actualizado correctamente', new ProductResource($product), null, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error General (Edición): " . $e->getMessage());
            return $this->response(false, 'Error al actualizar producto: ' , null, $e->getMessage(), 500);
        }
    }
    
    /**
     * CU-08: Buscar Producto 
     */
    public function search(Request $request) {
        $query = $request->input('query');

        $products = Product::where('codigo', 'LIKE', "%$query%")
            ->orWhere('name', 'LIKE', "%$query%")
            ->orWhere('description', 'LIKE', "%$query%")
            ->with(['productReceptions'])
            ->get();

        return $this->response(
            true, 
            'Resultados de búsqueda', 
            ProductResource::collection($products), 
            null, 
            200
        );
    }

    public function inventory(Request $request)
    {
        $query = $request->input('query');

        $products = Product::query()
            ->with(['productReceptions', 'category', 'supplier'])
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($subQuery) use ($query) {
                    $subQuery->where('codigo', 'LIKE', "%{$query}%")
                        ->orWhere('name', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%")
                        ->orWhere('location', 'LIKE', "%{$query}%");
                });
            })
            ->orderBy('name')
            ->get();

        return $this->response(
            true,
            'Inventario por productos',
            ProductResource::collection($products),
            null,
            200
        );
    }
}
