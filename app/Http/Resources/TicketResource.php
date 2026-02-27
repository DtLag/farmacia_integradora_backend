<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'user' => $this->user_id ? $this->user_id->name : null,
            'date_time' => $this->date_time,
            'customer' => $this->customer ? $this->customer->name : null,
            'payment_method' => $this->paymentMethod ? $this->paymentMethod->name : null,
            'total' => $this->total,
            'subtotal' => $this->subtotal,
            'details' => $this->details->map(function($detail) {
                return [
                    'product_name' => $detail->product ? $detail->product->name : null,
                    'amount' => $detail->amount,
                    'unit_price' => $detail->unit_price,
                    'subtotal' => $detail->subtotal,
                ];
            }),
        ];
    }
}
