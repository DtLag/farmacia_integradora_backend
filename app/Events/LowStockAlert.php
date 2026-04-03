<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $alertData;
    /**
     * Create a new event instance.
     */
    public function __construct($product)
    {
        $this->alertData = [
            'product_id' => $product->id,
            'name' => $product->name,
            'current_stock' => $product->stock,
            'min_stock' => $product->min_stock,
            'message' => "Alerta: El producto '{$product->name}' tiene un stock bajo de {$product->stock} unidades."
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('staff.alerts'),
        ];
    }

    public function broadcastAs(): String
    {
        return 'LowStock';
    }
}
