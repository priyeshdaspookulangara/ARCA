<?php

namespace Modules\HR\Benefits\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Benefits\Domain\Entities\EmployeeEnrollment;
use Modules\HR\Benefits\Domain\Entities\BenefitPlan;

class BenefitEnrollmentChangedEvent
{
    use Dispatchable, SerializesModels;

    public $employeeId;
    public $planId;
    public $deductionAmount;
    public $status;

    /**
     * Create a new event instance.
     *
     * @param EmployeeEnrollment $enrollment
     * @param BenefitPlan $plan
     */
    public function __construct(EmployeeEnrollment $enrollment, BenefitPlan $plan)
    {
        $this->employeeId = $enrollment->getEmployeeId();
        $this->planId = $enrollment->getPlanId();
        $this->status = $enrollment->getStatus();
        $this->deductionAmount = $plan->getDeductionAmount();
    }
}