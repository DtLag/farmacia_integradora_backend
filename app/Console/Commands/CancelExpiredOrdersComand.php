<?php

namespace App\Console\Commands;

use App\Events\PickUpStatusUpdated;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\PickUpReservation;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class CancelExpiredOrdersComand extends Command
{
    use ApiResponse;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cancel-expired-orders-comand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancelación automática de pedidos expirados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('state', 'pending')->where('scheduled_time', '<', now())->get();

        foreach($orders as $order){
            DB::transaction( function () use($order) {
                if($order->state !== 'pending'){
                    return;
                }
            
                $reservations = PickUpReservation::where('order_id', $order->id)->get();

                foreach($reservations as $reservation){
                    $product = $reservation->product;
                    $product->stock += $reservation->amount;
                    $product->save();
                
                    $reservation->state = 'canceled';
                    $reservation->save();

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'reason' => 'income',
                        'amount' => $reservation->amount,
                        'date_time' => now(),
                        'user_id' => null
                    ]);
                }
            
                $order->state = 'canceled';            
                $order->save();

                broadcast(new PickUpStatusUpdated($order));
            
                return $this->response(true, 'Pedido cancelado', $order, null, 200);
            });
        }        
    }
}
