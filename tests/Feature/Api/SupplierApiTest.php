<?php

namespace Tests\Feature\Api;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SupplierApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_list_suppliers(): void
    {
        Supplier::factory()->count(3)->create();

        $response = $this->getJson('/api/suppliers');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_create_supplier(): void
    {
        $supplierData = [
            'name' => 'Bamburi Distributor',
            'phone' => '0712345678',
            'email' => 'supplier@test.com',
            'address' => 'Nairobi',
        ];

        $response = $this->postJson('/api/suppliers', $supplierData);

        $response->assertCreated()
            ->assertJson($supplierData);

        $this->assertDatabaseHas('suppliers', $supplierData);
    }

    public function test_can_show_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->getJson("/api/suppliers/{$supplier->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $supplier->id,
                'name' => $supplier->name,
                'email' => $supplier->email,
                'phone' => $supplier->phone,
                'address' => $supplier->address,
            ]);
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $updatedData = [
            'name' => 'Updated Supplier',
            'email' => 'updated@test.com',
            'phone' => '0987654321',
            'address' => 'Updated Address',
        ];

        $response = $this->putJson("/api/suppliers/{$supplier->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson($updatedData);

        $this->assertDatabaseHas('suppliers', array_merge(['id' => $supplier->id], $updatedData));
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->deleteJson("/api/suppliers/{$supplier->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_validation_fails_on_create_with_invalid_data(): void
    {
        $response = $this->postJson('/api/suppliers', [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => str_repeat('1', 21), 
            'address' => $this->faker->text(1000), 
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone']);
    }

    public function test_validation_fails_on_update_with_invalid_data(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->putJson("/api/suppliers/{$supplier->id}", [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => str_repeat('1', 21),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone']);
    }

    public function test_unique_email_validation_on_create(): void
    {
        $existingSupplier = Supplier::factory()->create();

        $response = $this->postJson('/api/suppliers', [
            'name' => 'New Supplier',
            'email' => $existingSupplier->email,
            'phone' => '0712345678',
            'address' => 'New Address',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_unique_email_validation_on_update(): void
    {
        $supplier1 = Supplier::factory()->create();
        $supplier2 = Supplier::factory()->create();

        $response = $this->putJson("/api/suppliers/{$supplier1->id}", [
            'email' => $supplier2->email,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_can_update_partial_data(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->putJson("/api/suppliers/{$supplier->id}", [
            'name' => 'Partially Updated Name',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Partially Updated Name',
                'email' => $supplier->email, 
            ]);

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Partially Updated Name',
            'email' => $supplier->email,
        ]);
    }

    public function test_returns_404_for_nonexistent_supplier(): void
    {
        $response = $this->getJson('/api/suppliers/999');

        $response->assertStatus(404);
    }
}
