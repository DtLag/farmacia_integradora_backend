<?php

namespace App\Observers;

use App\Models\Batch;

class BatchObserver
{
    /**
     * Handle the Batch "created" event.
     */
    public function created(Batch $batch): void
    {
        $this->Audit(
            'Lotes', 
            'Nuevo Lote', 
            "Identificador: {$batch->identifier_batch}. Proveedor ID: {$batch->supplier_id}"
        );
    }

    /**
     * Handle the Batch "updated" event.
     */
    public function updated(Batch $batch): void
    {
        if ($batch->wasChanged(['identifier_batch', 'total', 'units_count'])) {
            $this->Audit(
                'Lotes', 
                'Actualización de Lote', 
                "Lote ID: {$batch->id}. Cambios: " . json_encode($batch->getChanges())
            );
        }
    }
}