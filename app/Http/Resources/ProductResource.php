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
            'presentation' => $this->presentation,
            'sale_price' => $this->sale_price,
            'location' => $this->location,
            'stock' => $this->stock,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'expiration_date' => $this->productReceptions()
                ->where('expiration_date', '>=', now())
                ->min('expiration_date'),
        ];
    }
}
