<?php

namespace Modules\HR\Recruitment\Application\Services;

use Modules\HR\Recruitment\Domain\Entities\JobOpening;
use Modules\HR\Recruitment\Domain\Entities\Applicant;
use Modules\HR\Recruitment\Domain\Entities\Application;
use Modules\HR\Recruitment\Domain\Repositories\JobOpeningRepositoryInterface;
use Modules\HR\Recruitment\Domain\Repositories\ApplicantRepositoryInterface;
use Modules\HR\Recruitment\Domain\Repositories\ApplicationRepositoryInterface;
use Modules\HR\Recruitment\Domain\Events\ApplicantHiredEvent;
use Illuminate\Contracts\Events\Dispatcher;

class RecruitmentService
{
    private $jobOpeningRepository;
    private $applicantRepository;
    private $applicationRepository;
    private $eventDispatcher;

    public function __construct(
        JobOpeningRepositoryInterface $jobOpeningRepository,
        ApplicantRepositoryInterface $applicantRepository,
        ApplicationRepositoryInterface $applicationRepository,
        Dispatcher $eventDispatcher
    ) {
        $this->jobOpeningRepository = $jobOpeningRepository;
        $this->applicantRepository = $applicantRepository;
        $this->applicationRepository = $applicationRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createJobOpening(string $positionId): JobOpening
    {
        $id = uniqid('jo_');
        $jobOpening = new JobOpening($id, $positionId);
        $this->jobOpeningRepository->save($jobOpening);
        return $jobOpening;
    }

    public function submitApplication(string $jobOpeningId, array $applicantData): Application
    {
        $applicant = $this->applicantRepository->findByEmail($applicantData['email']);
        if (!$applicant) {
            $applicantId = uniqid('appl_');
            $applicant = new Applicant(
                $applicantId,
                $applicantData['first_name'],
                $applicantData['last_name'],
                $applicantData['email'],
                $applicantData['phone'] ?? null
            );
            $this->applicantRepository->save($applicant);
        }

        $applicationId = uniqid('app_');
        $application = new Application($applicationId, $jobOpeningId, $applicant->getId());
        $this->applicationRepository->save($application);

        return $application;
    }

    public function updateApplicationStatus(string $applicationId, string $newStatus): ?Application
    {
        $application = $this->applicationRepository->findById($applicationId);
        if ($application) {
            $application->updateStatus($newStatus);
            $this->applicationRepository->save($application);

            if ($newStatus === 'hired') {
                $applicant = $this->applicantRepository->findById($application->getApplicantId());
                if ($applicant) {
                    $this->eventDispatcher->dispatch(new ApplicantHiredEvent($applicant, $application));
                }
            }
        }
        return $application;
    }

    public function getJobOpenings(): array
    {
        return $this->jobOpeningRepository->findAll();
    }

    public function getApplicationsForJobOpening(string $jobOpeningId): array
    {
        return $this->applicationRepository->findByJobOpening($jobOpeningId);
    }
}