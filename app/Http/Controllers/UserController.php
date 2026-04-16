<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private function isAdmin(Request $request): bool
    {
        return $request->user()?->loadMissing('role')->role?->slug === 'admin';
    }

    public function index(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $query = User::with('role');

        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        return response()->json($query->get());
    }

    public function show(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = User::withTrashed()->with('role')->findOrFail($id);

        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = User::withTrashed()->findOrFail($id);
        $validated = $request->validated();

        if (array_key_exists('role', $validated)) {
            $role = Role::where('slug', $validated['role'])->first();

            if (!$role) {
                return response()->json(['message' => 'Rol no valido'], 422);
            }

            $validated['role_id'] = $role->id;
            unset($validated['role']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Usuario actualizado',
            'user' => $user->fresh()->load('role'),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario desactivado correctamente']);
    }

    public function restore(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'message' => 'Usuario restaurado correctamente',
            'user' => $user->fresh()->load('role'),
        ]);
    }

    public function staff()
    {
        $users = User::withTrashed()
            ->with('role')
            ->where('role_id', '!=', 3)
            ->get();

        return response()->json([
            'success' => true,
            'count' => $users->count(),
            'data' => $users,
        ]);
    }
}
