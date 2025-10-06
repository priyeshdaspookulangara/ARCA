<?php

namespace Modules\HR\Recruitment\Infrastructure\Persistence;

use Modules\HR\Recruitment\Domain\Entities\Application;
use Modules\HR\Recruitment\Domain\Repositories\ApplicationRepositoryInterface;

class InMemoryApplicationRepository implements ApplicationRepositoryInterface
{
    private $applications = [];

    public function findById(string $id): ?Application
    {
        return $this->applications[$id] ?? null;
    }

    public function findByJobOpening(string $jobOpeningId): array
    {
        return array_filter($this->applications, function (Application $application) use ($jobOpeningId) {
            return $application->getJobOpeningId() === $jobOpeningId;
        });
    }

    public function save(Application $application): void
    {
        $this->applications[$application->getId()] = $application;
    }
}