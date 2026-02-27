<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ProductReception;

class ReceptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'identifier_batch' => $this->identifier_batch,
            'supplier' => $this->supplier ? $this->supplier->name : null,
            'entry_date' => $this->entry_date,
            'notes' => $this->notes,
            'products_count' => $this->products_count,
            'units_count' => $this->units_count,
            'total' => $this->total,
            'batch_products' => ProductReception::where('batch_id', $this->id)->with('product')->get()->map(function ($reception) {
                return [
                    'product' => new ProductResource($reception->product),
                    'amount' => $reception->amount,
                    'unit_price' => $reception->unit_price,
                    'expiration_date' => $reception->expiration_date,   
                ];
            }),
            /*
            'products' => $this->products()->with('product')->get()->map(function ($reception) {
                return [
                    'product' => new ProductResource($reception->product),
                    'amount' => $reception->amount,
                    'unit_price' => $reception->unit_price,
                    'expiration_date' => $reception->expiration_date,   
        ];
            }),
            */
        ];  
    
}
}
