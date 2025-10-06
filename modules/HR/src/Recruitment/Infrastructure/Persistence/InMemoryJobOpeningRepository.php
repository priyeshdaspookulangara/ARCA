<?php

namespace Modules\HR\Recruitment\Infrastructure\Persistence;

use Modules\HR\Recruitment\Domain\Entities\JobOpening;
use Modules\HR\Recruitment\Domain\Repositories\JobOpeningRepositoryInterface;

class InMemoryJobOpeningRepository implements JobOpeningRepositoryInterface
{
    private $jobOpenings = [];

    public function findById(string $id): ?JobOpening
    {
        return $this->jobOpenings[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->jobOpenings);
    }

    public function save(JobOpening $jobOpening): void
    {
        $this->jobOpenings[$jobOpening->getId()] = $jobOpening;
    }

    public function delete(string $id): void
    {
        unset($this->jobOpenings[$id]);
    }
}