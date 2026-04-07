<?php

namespace App\Observers;

use App\Models\PickUpReservation;

class PickUpReservationObserver
{
    /**
     * Handle the PickUpReservation "created" event.
     */
    public function created(PickUpReservation $pickUpReservation): void
    {
        $this->Audit(
            'Reservaciones', 
            'Nueva Reservación', 
            "Producto ID: {$pickUpReservation->product_id}. Cantidad apartada: {$pickUpReservation->amount}. Orden vinculada: " . ($pickUpReservation->order_id ?? 'Ninguna')
        );
    }

    /**
     * Handle the PickUpReservation "updated" event.
     */
    public function updated(PickUpReservation $pickUpReservation): void
    {
        // Vigilamos cambios de estado (pending, completed, canceled)
        if ($pickUpReservation->wasChanged('state')) {
            $nuevoEstado = $pickUpReservation->state;
            $estadoAnterior = $pickUpReservation->getOriginal('state');

            $this->recordAudit(
                'Reservaciones', 
                'Cambio de Estado', 
                "Reservación ID: {$pickUpReservation->id} cambió de '{$estadoAnterior}' a '{$nuevoEstado}'."
            );
        }

        // Vigilamos si se altera la cantidad apartada
        if ($pickUpReservation->wasChanged('amount')) {
            $this->Audit(
                'Reservaciones', 
                'Ajuste de Cantidad', 
                "Reservación ID: {$pickUpReservation->id}. Cantidad anterior: " . $pickUpReservation->getOriginal('amount') . ". Nueva cantidad: {$pickUpReservation->amount}."
            );
        }
    }

    /**
     * Handle the PickUpReservation "deleted" event.
     */
    public function deleted(PickUpReservation $pickUpReservation): void
    {
        $this->Audit(
            'Reservaciones', 
            'Eliminación', 
            "Se eliminó el registro de reservación ID: {$pickUpReservation->id} para el Producto ID: {$pickUpReservation->product_id}."
        );
    }

    /**
     * Handle the PickUpReservation "restored" event.
     */
    public function restored(PickUpReservation $pickUpReservation): void
    {
        $this->Audit(
            'Reservaciones', 
            'Restauración', 
            "Se restauró la reservación ID: {$pickUpReservation->id}."
        );
    }
}
