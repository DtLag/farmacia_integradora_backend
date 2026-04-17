<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !isset($user->role_id)) {
            return response()->json([
                'message' => 'Acceso denegado. Esta acción es exclusiva para el personal.'
            ], 403);
        }

        return $next($request);
    }
}