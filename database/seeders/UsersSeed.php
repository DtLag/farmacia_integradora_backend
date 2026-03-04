<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'role' => 'admin',
        ]);
    }
}
