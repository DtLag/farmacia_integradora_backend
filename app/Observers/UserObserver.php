<?php

namespace App\Observers;

use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class UserObserver implements ShouldHandleEventsAfterCommit
{
    use Auditable; 
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->Audit('Usuario', 'Creado', "Se creó el usuario: {$user->name}, con Rol: {$user->role->name} (ID: {$user->id})");
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged(['role_id', 'email', 'password'])) {
            $cambios = $user->getChanges();

            if (isset($cambios['password'])) $cambios['password'] = '********';
            
            $this->Audit(
                'Usuarios', 
                'Actualización', 
                "ID: {$user->id}. Cambios detectados: " . json_encode($cambios)
            );
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->Audit('Usuarios', 'Baja', "Se aplicó borrado lógico al usuario ID: {$user->id} ({$user->email})");
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $this->Audit('Usuarios', 'Restaurado', "Se restauró el usuario ID: {$user->id} ({$user->email})");
    }
}
