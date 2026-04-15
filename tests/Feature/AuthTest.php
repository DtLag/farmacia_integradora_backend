<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Models\Customer;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\UsersSeed::class]);
});

test('TP-01: Login de Staff - Credenciales válidas', function () {
    $response = $this->postJson('/api/login/staff', [
        'email' => 'admin@test.com',
        'password' => 'password123'
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'token',
                 'user' => ['role']
             ]);
});

test('TP-02: Login de staff - Contraseña incorrecta', function () {
    $response = $this->postJson('/api/login/staff', [
        'email' => 'admin@test.com',
        'password' => 'wrongpass'
    ]);

    $response->assertStatus(422);
    $this->assertArrayNotHasKey('token', $response->json());
});

test('TP-03: Login de cliente - Cuenta Verificada', function () {
    $response = $this->postJson('/api/login/customer', [
        'email' => 'cliente@test.com',
        'password' => 'pass1234'
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure(['token', 'user']);
});

test('TP-04: Registro de cliente - envío de código', function () {
    Mail::fake();

    $response = $this->postJson('/api/register/customer', [
        'name' => 'Pedro TQM',
        'email' => 'pedrito@test.com',
        'password' => 'pass1234'
    ]);

    $response->assertStatus(201); 
    
    $this->assertDatabaseHas('customers', [
        'email' => 'pedrito@test.com',
        'is_verified' => false
    ]);
});

test('TP-05: Verificar código de email - código válido', function () {
    Customer::create([
        'name' => 'Pedro TQM',
        'email' => 'pedrito@test.com',
        'password' => bcrypt('pass123'),
        'verification_code' => bcrypt('1234'),
        'is_verified' => false,
        'verification_code_expires_at' => now()->addMinutes(10)
    ]);

    $response = $this->postJson('/api/verify-code', [
        'email' => 'pedrito@test.com',
        'code' => '1234'
    ]);

    $response->assertStatus(200);
    
    $this->assertDatabaseHas('customers', [
        'email' => 'pedrito@test.com',
        'is_verified' => true 
    ]);
});

test('TP-06: Verificar código - código expirado', function () {
    Customer::create([
        'name' => 'Pedro TQM',
        'email' => 'pedrito@test.com',
        'password' => bcrypt('pass123'),
        'verification_code' => bcrypt('1234'),
        'is_verified' => false,
        'verification_code_expires_at' => now()->subMinutes(11) 
    ]);

    $response = $this->postJson('/api/verify-code', [
        'email' => 'pedrito@test.com',
        'code' => '1234'
    ]);

    $response->assertStatus(400);

    $this->assertDatabaseHas('customers', [
        'email' => 'pedrito@test.com',
        'is_verified' => false 
    ]);
});

test('TP-07: Registro de Staff - sin rol admin', function () {
    $loginResponse = $this->postJson('/api/login/staff', [
        'email' => 'empleado@test.com',
        'password' => 'pass123'
    ]);
    
    $token = $loginResponse->json('token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson('/api/register/staff', [
        'name' => 'Diegogo',
        'last_name' => 'Gogogo',
        'email' => 'diegozzz@prueba.com',
        'user_id' => '12345',
        'password' => 'password',
        'role_id' => 2
    ]);

    $response->assertStatus(403);
});