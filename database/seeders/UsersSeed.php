<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersSeed extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Insertamos el Staff en la tabla users (SIN is_verified)
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'last_name' => 'Test',
                'user_id' => '00001',
                'email' => 'admin@test.com',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Empleado',
                'last_name' => 'Test',
                'user_id' => '00002',
                'email' => 'empleado@test.com',
                'password' => Hash::make('pass123'),
                'role_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        // 2. Insertamos el Cliente en la tabla customers (CON is_verified)
        DB::table('customers')->insert([
            [
                'name' => 'Cliente',
                'email' => 'cliente@test.com',
                'password' => Hash::make('pass1234'),
                'is_verified' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}