<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // Listar proveedores 
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $query = Supplier::query();

        if ($request->has('with_trashed')) {
            $query->withTrashed();
        }

        $suppliers = $query->get();

        return response()->json($suppliers);
    }

    // Mostrar un proveedor específico
    public function show(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $supplier = Supplier::withTrashed()->findOrFail($id);

        return response()->json($supplier);
    }

    // Crear un nuevo proveedor
    public function store(StoreSupplierRequest $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validated();

        $supplier = Supplier::create($validated);

        return response()->json(['message' => 'Proveedor creado', 'supplier' => $supplier], 201);
    }

    // Actualizar un proveedor
    public function update(UpdateSupplierRequest $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $supplier = Supplier::withTrashed()->findOrFail($id);

        $validated = $request->validated();

        $supplier->update($validated);

        return response()->json(['message' => 'Proveedor actualizado', 'supplier' => $supplier]);
    }

    // Eliminar (soft delete) un proveedor
    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json(['message' => 'Proveedor desactivado correctamente']);
    }

    // Restaurar un proveedor eliminado
    public function restore(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $supplier = Supplier::onlyTrashed()->findOrFail($id);
        $supplier->restore();

        return response()->json(['message' => 'Proveedor restaurado correctamente', 'supplier' => $supplier]);
    }
}
