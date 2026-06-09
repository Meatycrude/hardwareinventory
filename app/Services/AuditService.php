<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    public static function log(
        ?int $userId,
        string $action,
        string $description,
        array $metadata = []
    ): void {

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}