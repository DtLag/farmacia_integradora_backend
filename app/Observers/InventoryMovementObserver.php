<?php

namespace App\Observers;

use App\Models\InventoryMovement;
use App\Traits\Auditable;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class InventoryMovementObserver implements ShouldHandleEventsAfterCommit
{
    use Auditable;
    /**
     * Handle the InventoryMovement "created" event.
     */
    public function created(InventoryMovement $inventoryMovement): void
    {
        $this->Audit(
            'Movimientos de Inventario', 
            ucfirst($inventoryMovement->reason), 
            "Ajuste de stock para Producto ID: {$inventoryMovement->product_id}. Cantidad: {$inventoryMovement->amount}. Motivo: {$inventoryMovement->reason}"
        );
    }

    /**
     * Handle the InventoryMovement "updated" event.
     */
    public function updated(InventoryMovement $inventoryMovement): void
    {
        $this->Audit(
            'Movimientos de Inventario', 
            'Modificación de Registro', 
            "Se alteró un registro de movimiento previo (ID: {$inventoryMovement->id})"
        );
    }
}
