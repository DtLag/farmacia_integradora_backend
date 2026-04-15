<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'codigo' => $this->codigo,
            'name' => $this->name,
            'description' => $this->description,
            'presentation' => $this->presentation,
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'location' => $this->location,
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'category_name' => $this->category?->name,
            'supplier_name' => $this->supplier?->name,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'expiration_date' => $this->productReceptions()
                ->where('expiration_date', '>=', now())
                ->min('expiration_date'),
        ];
    }
}
