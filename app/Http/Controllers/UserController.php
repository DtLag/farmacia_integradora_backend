<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    // Listar todos los usuarios
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Obtener usuarios
        $query = User::query();

        if ($request->has('with_trashed')) {
            $query->withTrashed();
        }

        $users = $query->get();

        return response()->json($users);
    }

    // Mostrar un usuario específico
    public function show(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = User::withTrashed()->findOrFail($id);

        return response()->json($user);
    }

    // Actualizar un usuario
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = User::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|string|in:admin,vendedor',
            'password' => 'sometimes|string|min:8'
        ]);

        $user->update($validated);

        return response()->json(['message' => 'Usuario actualizado', 'user' => $user]);
    }

    // Eliminar un usuario (Soft Delete)
    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario desactivado correctamente']);
    }

    // Restaurar un usuario eliminado
    public function restore(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = User::onlyTrashed()->findOrFail($id);

        $user->restore();

        return response()->json(['message' => 'Usuario restaurado correctamente', 'user' => $user]);
    }
}
