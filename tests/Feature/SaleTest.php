<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\sale;
class SaleTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_create_sale(): void        
    {
      $sale = sale::factory()->create();
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'total_amount' => $sale->total_amount,
            'payment_method' => $sale->payment_method,
            'user_id' => $sale->user_id,
        ]);

    }

        public function test_update_sale(): void
        {
            $sale = sale::factory()->create();
    
            $sale->update([
                'invoice_number' => 'INV-54321',
                'total_amount' => 150,
                'payment_method' => 'card',
                'user_id' => null, 
            ]);
    
            $this->assertDatabaseHas('sales', [
                'id' => $sale->id,
                'invoice_number' => 'INV-54321',
                'total_amount' => 150,
                'payment_method' => 'card',
                'user_id' => null, 
            ]);
        }

        public function test_delete_sale(): void
        {
            $sale = sale::factory()->create();
    
            $sale->delete();
    
            $this->assertDatabaseMissing('sales', [
                'id' => $sale->id,
            ]);
        }

        public function test_sale_factory_creates_valid_values(): void
        {
            $sale = sale::factory()->create();
    
            $this->assertNotNull($sale->invoice_number);
            $this->assertGreaterThan(0, $sale->total_amount);
            $this->assertContains($sale->payment_method, ['cash', 'mpesa', 'bank', 'card']);
        }
}
