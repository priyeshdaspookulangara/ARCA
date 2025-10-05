<?php

namespace Modules\HR\OrganizationalManagement\Application\Services;

use Modules\HR\OrganizationalManagement\Domain\Entities\Job;
use Modules\HR\OrganizationalManagement\Domain\Repositories\JobRepositoryInterface;

class JobService
{
    private $jobRepository;

    public function __construct(JobRepositoryInterface $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function createJob(string $title): Job
    {
        $id = uniqid('job_');
        $job = new Job($id, $title);
        $this->jobRepository->save($job);
        return $job;
    }

    public function getJob(string $id): ?Job
    {
        return $this->jobRepository->findById($id);
    }

    public function getAllJobs(): array
    {
        return $this->jobRepository->findAll();
    }

    public function deleteJob(string $id): void
    {
        $this->jobRepository->delete($id);
    }
}