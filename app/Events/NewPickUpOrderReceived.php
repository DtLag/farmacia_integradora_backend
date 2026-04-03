<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPickUpOrderReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderData;
    /**
     * Create a new event instance.
     */
    public function __construct($order)
    {
        $this->orderData = [
            'id' => $order->id,
            'customer_name' => $order->customer_name ?? 'N/A',
            'total' => $order->total,
            'date_time' => $order->created_at->format('Y-m-d H:i:s'),
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
            new PrivateChannel('staff.orders'),
        ];
    }

    public function broadcastAs(): String
    {
        return 'NewPickUpOrder';
    }
}
