<?php

namespace Modules\GRC\AuditMgt\Domain;

use Illuminate\Support\Collection;
use Modules\GRC\AuditMgt\Domain\Model\AuditLog;

interface AuditLogRepositoryInterface
{
    public function findById(int $id): ?AuditLog;

    public function getAll(): Collection;

    public function save(AuditLog $auditLog): AuditLog;

    public function delete(AuditLog $auditLog): bool;
}