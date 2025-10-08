<?php

namespace Modules\HR\Benefits\Application\Services;

use Modules\HR\Benefits\Domain\Entities\BenefitPlan;
use Modules\HR\Benefits\Domain\Entities\BenefitPlan;
use Modules\HR\Benefits\Domain\Entities\EmployeeEnrollment;
use Modules\HR\Benefits\Domain\Repositories\BenefitPlanRepositoryInterface;
use Modules\HR\Benefits\Domain\Repositories\EmployeeEnrollmentRepositoryInterface;
use Modules\HR\Benefits\Domain\Events\BenefitEnrollmentChangedEvent;
use Illuminate\Contracts\Events\Dispatcher;

class BenefitsService
{
    private $benefitPlanRepository;
    private $employeeEnrollmentRepository;
    private $eventDispatcher;

    public function __construct(
        BenefitPlanRepositoryInterface $benefitPlanRepository,
        EmployeeEnrollmentRepositoryInterface $employeeEnrollmentRepository,
        Dispatcher $eventDispatcher
    ) {
        $this->benefitPlanRepository = $benefitPlanRepository;
        $this->employeeEnrollmentRepository = $employeeEnrollmentRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createBenefitPlan(string $name, string $type, float $deductionAmount): BenefitPlan
    {
        $id = uniqid('bp_');
        $benefitPlan = new BenefitPlan($id, $name, $type, $deductionAmount);
        $this->benefitPlanRepository->save($benefitPlan);
        return $benefitPlan;
    }

    public function getBenefitPlans(): array
    {
        return $this->benefitPlanRepository->findAll();
    }

    public function enrollEmployeeInBenefit(string $employeeId, string $planId): EmployeeEnrollment
    {
        $id = uniqid('en_');
        $enrollment = new EmployeeEnrollment($id, $employeeId, $planId);
        $this->employeeEnrollmentRepository->save($enrollment);

        $plan = $this->benefitPlanRepository->findById($planId);
        if ($plan) {
            $this->eventDispatcher->dispatch(new BenefitEnrollmentChangedEvent($enrollment, $plan));
        }

        return $enrollment;
    }

    public function getEmployeeEnrollments(string $employeeId): array
    {
        return $this->employeeEnrollmentRepository->findByEmployee($employeeId);
    }
}