<?php

namespace Modules\HR\OrganizationalManagement\Infrastructure\Persistence;

use Modules\HR\OrganizationalManagement\Domain\Entities\Job;
use Modules\HR\OrganizationalManagement\Domain\Repositories\JobRepositoryInterface;

class InMemoryJobRepository implements JobRepositoryInterface
{
    private $jobs = [];

    public function findById(string $id): ?Job
    {
        return $this->jobs[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->jobs);
    }

    public function save(Job $job): void
    {
        $this->jobs[$job->getId()] = $job;
    }

    public function delete(string $id): void
    {
        unset($this->jobs[$id]);
    }
}