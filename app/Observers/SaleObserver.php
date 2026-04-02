<?php

namespace App\Observers;

use App\Models\Sale;
use App\Traits\Auditable;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class SaleObserver implements ShouldHandleEventsAfterCommit
{
    use Auditable;
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        $this->Audit('Venta', 'Nueva Venta', "Venta ID: {$sale->id} por un total de: {$sale->total_amount}. Cliente ID: " . ($sale->customer_id ?? 'N/A'));
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        if ($sale->wasChanged('state')) {
            $nuevoEstado = $sale->state;
            $estadoAnterior = $sale->getOriginal('state');
            $this->Audit('Venta', ' Cambio de Estado', "Venta #{$sale->id} cambió de '{$estadoAnterior}' a '{$nuevoEstado}'.");
        }
    }

    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        $this->Audit('Venta', 'Venta Cancelada', "Venta ID: {$sale->id} ha sido cancelada. Total: {$sale->total_amount}. Cliente ID: " . ($sale->customer_id ?? 'N/A'));
    }

    /**
     * Handle the Sale "restored" event.
     */
    public function restored(Sale $sale): void
    {
        $this->Audit('Venta', 'Venta Restaurada', "Venta ID: {$sale->id} ha sido restaurada. Total: {$sale->total_amount}. Cliente ID: " . ($sale->customer_id ?? 'N/A'));
    }
}
