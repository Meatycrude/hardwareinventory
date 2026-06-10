<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_can_be_created(): void
    {
        $user = User::factory()->create();

        $audit = AuditLog::create([
            'user_id' => $user->id,
            'action' => 'product.created',
            'description' => 'Created product Simba Cement',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'id' => $audit->id,
            'user_id' => $user->id,
            'action' => 'product.created',
        ]);
    }

    public function test_product_creation_creates_audit_log(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create();

        $supplier = Supplier::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/products', [
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
                'name' => 'Simba Cement',
                'brand' => 'Bamburi',
                'unit' => 'Bag',
                'buying_price' => 800,
                'selling_price' => 1000,
                'stock_quantity' => 10,
                'minimum_stock' => 5,
                'description' => 'Test cement product',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'product.created',
        ]);
    }

    public function test_product_update_creates_audit_log(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'sku' => 'PROD-001',
            'name' => 'Old Cement',
            'stock_quantity' => 10,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'sku' => 'PROD-001',
                'name' => 'Updated Cement',
                'stock_quantity' => 20,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'product.updated',
        ]);
    }

    public function test_product_delete_creates_audit_log(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::factory()->create([
            'name' => 'Deleted Cement',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson("/api/products/{$product->id}");

        $response->assertNoContent();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'product.deleted',
        ]);
    }

    public function test_product_restock_creates_audit_log(): void
    {
        $user = User::factory()->create([
            'role' => 'storekeeper',
        ]);

        $product = Product::factory()->create([
            'stock_quantity' => 5,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson("/api/products/{$product->id}/restock", [
                'quantity' => 10,
                'buying_price' => 700,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'product.restocked',
        ]);
    }

    public function test_sale_creation_creates_audit_log(): void
    {
        $user = User::factory()->create([
            'role' => 'cashier',
        ]);

        $product = Product::factory()->create([
            'selling_price' => 100,
            'stock_quantity' => 20,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/sales', [
                'payment_method' => 'cash',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                    ],
                ],
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'sale.created',
        ]);
    }

    public function test_receipt_generation_creates_audit_log(): void
    {
        $user = User::factory()->create([
            'role' => 'cashier',
        ]);

        $sale = Sale::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson("/api/sales/{$sale->id}/receipt");

        $response->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'receipt.generated',
        ]);
    }

    public function test_two_factor_verification_creates_audit_log(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'two_factor_code' => '123456',
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/verify-2fa', [
            'email' => $user->email,
            'code' => '123456',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => '2fa.verified',
        ]);
    }

    public function test_logout_creates_audit_log(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout');

        $response->assertNoContent();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'user.logged_out',
        ]);
    }

    public function test_login_creates_audit_log(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'user.logged_in',
        ]);
    }

    public function test_password_change_creates_audit_log(): void
    {

        $user = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('oldpassword'),
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/profile/password', [
                'current_password' => 'oldpassword',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'password.changed',
        ]);
    }

    public function test_user_creation_creates_audit_log(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'cashier',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'user.created',
        ]);
    }
}
