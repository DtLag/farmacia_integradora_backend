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
     */                             // 1. Validación
    public function registerProduct(ProductRequest $request)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();

        

        try {
            DB::beginTransaction();

            // 2. Manejo de Imagen (Lógica HEAD)
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('product_images', 'public');
                $request['image'] = $imagePath;
            } else {
                $request['image'] = null;
            }
            
            // 3. Crear producto
            $product = Product::create($request->all());

            // 4. Auditoría
            try {
                Audit::create([
                    'user_id' => Auth::id(),
                    'affected_module' => 'Productos',
                    'action_performed' => 'Registro',
                    'detail' => "Se registró nuevo producto: {$product->name}",
                    'date_time' => now()
                ]);
            } catch (\Exception $e) {
                Log::error("Error de Auditoría (Registro): " . $e->getMessage());
            }

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
     */                    // 1. Validación
    public function update(UpdateReceptionRequest $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }


        try {
            DB::beginTransaction();

            // 2. Manejo de Imagen en Edición
            if ($request->hasFile('image')) {
                // Borrar imagen anterior si existe
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('product_images', 'public');
                $request['image'] = $imagePath;
            }

            // 3. Guardar cambios
            $product->update($request->all());

            // 4. Auditoría
            try {
                Audit::create([
                    'user_id' => Auth::id(),
                    'affected_module' => 'Productos',
                    'action_performed' => 'Edición',
                    'detail' => "Se actualizó la información del producto ID {$product->id}: {$product->name}",
                    'date_time' => now()
                ]);
            } catch (\Exception $e) {
                Log::error("Error de Auditoría (Edición): " . $e->getMessage());
            }

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
}
