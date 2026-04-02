<?php

namespace App\Observers;

use App\Models\Product;
use App\Traits\Auditable;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ProductObserver implements ShouldHandleEventsAfterCommit
{
    use Auditable;
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->Audit('Producto', 'Creado', "Se registro el producto: {$product->name}");
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if ($product->wasChanged(['stock', 'sale_price', 'location'])) {
            $changes = json_encode($product->getChanges());
            $this->Audit('Productos', 'Actualizado', "ID: {$product->id}. Cambios: {$changes}");
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->Audit('Producto', 'Eliminado', "Se eliminó el producto: {$product->name}");
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $this->Audit('Producto', 'Restaurado', "Se restauró el producto: {$product->name}");
    }
}
