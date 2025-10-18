<?php

namespace Modules\GRC\ProcessControl\Application;

class SoDViolationHandler
{
    /**
     * Handle an SoD violation.
     *
     * @param string $enforcementMode
     * @param array $violationDetails
     * @return void
     */
    public function handle(string $enforcementMode, array $violationDetails)
    {
        switch ($enforcementMode) {
            case 'warn':
                // Log the warning
                \Log::warning('SoD Violation (Warn)', $violationDetails);
                break;
            case 'block':
                // In a real implementation, this would throw an exception to block the transaction.
                \Log::error('SoD Violation (Block)', $violationDetails);
                break;
            case 'require_approval':
                // In a real implementation, this would create a task for a manager to approve.
                \Log::info('SoD Violation (Require Approval)', $violationDetails);
                break;
        }
    }
}