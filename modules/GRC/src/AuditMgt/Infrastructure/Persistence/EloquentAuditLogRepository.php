<?php

namespace Modules\GRC\AuditMgt\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\GRC\AuditMgt\Domain\AuditLogRepositoryInterface;
use Modules\GRC\AuditMgt\Domain\Model\AuditLog;

class EloquentAuditLogRepository implements AuditLogRepositoryInterface
{
    public function findById(int $id): ?AuditLog
    {
        return AuditLog::find($id);
    }

    public function getAll(): Collection
    {
        return AuditLog::all();
    }

    public function save(AuditLog $auditLog): AuditLog
    {
        $auditLog->save();
        return $auditLog;
    }

    public function delete(AuditLog $auditLog): bool
    {
        return $auditLog->delete();
    }
}