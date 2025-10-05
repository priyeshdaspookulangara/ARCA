<?php

namespace Modules\HR\OrganizationalManagement\Domain\Repositories;

use Modules\HR\OrganizationalManagement\Domain\Entities\Job;

interface JobRepositoryInterface
{
    public function findById(string $id): ?Job;

    public function findAll(): array;

    public function save(Job $job): void;

    public function delete(string $id): void;
}