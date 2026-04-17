<?php

namespace App\Observers;

use App\Models\ProductReception;
use App\Traits\Auditable;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ProductReceptionObserver implements ShouldHandleEventsAfterCommit
{
    use Auditable;
    /**
     * Handle the ProductReception "created" event.
     */
    public function created(ProductReception $productReception): void
    {
        $this->Audit('Recepción de Productos', 'Nueva Recepción', "Recepción ID: {$productReception->product_id}. Cantidad: {$productReception->amount}. Proveedor ID: {$productReception->supplier_id}");
    }

    /**
     * Handle the ProductReception "updated" event.
     */
    public function updated(ProductReception $productReception): void
    {
        if ($reception->wasChanged(['amount', 'unit_price', 'expiration_date'])) {
            $this->Audit(
                'Recepciones', 
                'Corrección de Recepción', 
                "Modificación en recepción ID: {$reception->id}. Cambios: " . json_encode($reception->getChanges())
            );
        }
    }

    /**
     * Handle the ProductReception "deleted" event.
     */
    public function deleted(ProductReception $productReception): void
    {
        $this->Audit('Recepciones', 'Anulación', "Se eliminó el registro de recepción ID: {$reception->id}");
    }
}
