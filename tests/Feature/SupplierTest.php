<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_adding_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'phone' => $supplier->phone,
            'email' => $supplier->email,
            'address' => $supplier->address,
        ]);
        $this->assertNotNull($supplier->name);
        $this->assertNotNull($supplier->email);
        $this->assertNotNull($supplier->phone);
        $this->assertNotNull($supplier->address);

    }

    public function test_updating_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $supplier->update([
            'name' => 'Updated Supplier Name',
            'phone' => '1234567890',
            'email' => 'updated@example.com',
            'address' => '123 Updated Street',
        ]);

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Updated Supplier Name',
            'phone' => '1234567890',
            'email' => 'updated@example.com',
            'address' => '123 Updated Street',
        ]);
    }
}
