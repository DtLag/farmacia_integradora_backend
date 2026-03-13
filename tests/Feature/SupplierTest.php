<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // create admin user
        $this->admin = User::factory()->create([        
            'role' => 'admin',
            'password' => bcrypt('secret'),
        ]);
    }

    public function test_admin_can_crud_suppliers()
    {
        $this->actingAs($this->admin, 'sanctum');

        // create
        $response = $this->postJson('/api/suppliers', [
            'name' => 'Acme Corp',
            'contact' => 'John Doe',
            'email' => 'acme@example.com',
            'phone_number' => '12345678'
        ]);
        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Acme Corp']);

        $supplierId = $response->json('supplier.id');

        // read
        $this->getJson("/api/suppliers/{$supplierId}")
             ->assertStatus(200)
             ->assertJsonFragment(['email' => 'acme@example.com']);

        // update
        $this->putJson("/api/suppliers/{$supplierId}", ['phone_number' => '87654321'])
             ->assertStatus(200)
             ->assertJsonFragment(['phone_number' => '87654321']);

        // delete
        $this->deleteJson("/api/suppliers/{$supplierId}")
             ->assertStatus(200);

        // verify trashed
        $this->getJson('/api/suppliers?with_trashed=1')
             ->assertJsonFragment(['id' => $supplierId]);

        // restore
        $this->postJson("/api/suppliers/{$supplierId}/restore")
             ->assertStatus(200)
             ->assertJsonFragment(['id' => $supplierId]);
    }

    public function test_non_admin_cannot_manage_suppliers()
    {
        $user = User::factory()->create(['role' => 'vendedor']);
        $this->actingAs($user, 'sanctum');

        $this->getJson('/api/suppliers')->assertStatus(403);
        $this->postJson('/api/suppliers', ['name' => 'foo'])->assertStatus(403);
    }
}
