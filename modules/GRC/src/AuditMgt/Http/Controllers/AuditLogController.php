<?php

namespace Modules\GRC\AuditMgt\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\GRC\AuditMgt\Domain\AuditLogRepositoryInterface;

class AuditLogController extends Controller
{
    private $auditLogRepository;

    public function __construct(AuditLogRepositoryInterface $auditLogRepository)
    {
        $this->auditLogRepository = $auditLogRepository;
    }

    public function query(Request $request)
    {
        // In a real implementation, this would use the request parameters
        // to filter the audit logs.
        return $this->auditLogRepository->getAll();
    }
}