<?php

namespace Tests\Feature\Api;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        AuditLog::factory()->count(5)->create();

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/audit-logs');

        $response->assertOk()
            ->assertJsonCount(5);
    }

    public function test_cashier_cannot_view_audit_logs(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $response = $this
            ->actingAs($cashier, 'sanctum')
            ->getJson('/api/audit-logs');

        $response->assertForbidden();
    }

    public function test_storekeeper_cannot_view_audit_logs(): void
    {
        $storekeeper = User::factory()->create([
            'role' => 'storekeeper',
        ]);

        $response = $this
            ->actingAs($storekeeper, 'sanctum')
            ->getJson('/api/audit-logs');

        $response->assertForbidden();
    }
}