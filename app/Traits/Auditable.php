<?php

namespace App\Traits;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected function Audit(string $module, string $action, ?string $detail = null)
    {
        $user = Auth::user();
        $userId = null;

        if ($user instanceof User){
            $userId = $user->id;
        }

        Audit::create([
            'user_id' => $userId,
            'affected_module' => $module,
            'action_performed' => $action,
            'date_time' => now(),
            'detail' => $detail,
        ]);
    }
}