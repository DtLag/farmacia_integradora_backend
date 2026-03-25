<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UsersSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@gmail.com',
            'user_id' => 1,
            'password' => Hash::make('123456'),
            'role' => '1', // Asumiendo que el rol admin tiene ID 1
        ]);

        Role::create([
            'name' => 'Administrador',
            'slug' => 'admin',
            'description' => 'Usuario con permisos de administración',
        ]);
    }
}
