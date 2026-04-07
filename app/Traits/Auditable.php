<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected function Audit(string $module, string $action, string $detail = null)
    {
        Audit::create([
            'user_id' => Auth::id(),
            'affected_module' => $module,
            'action_performed' => $action,
            'date_time' => now(),
            'detail' => $detail,
        ]);
    }
}