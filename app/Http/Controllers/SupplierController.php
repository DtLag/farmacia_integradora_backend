<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Traits\ApiResponse;

class SupplierController extends Controller
{
    use ApiResponse;
    public function index()
    {
        // Lógica para listar proveedores
        $suppliers = Supplier::all();
        return $this->response(true, 'Suppliers retrieved successfully', $suppliers);
    }
}
