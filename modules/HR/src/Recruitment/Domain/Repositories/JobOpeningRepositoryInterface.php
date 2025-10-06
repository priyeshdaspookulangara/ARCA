<?php

namespace Modules\HR\Recruitment\Domain\Repositories;

use Modules\HR\Recruitment\Domain\Entities\JobOpening;

interface JobOpeningRepositoryInterface
{
    public function findById(string $id): ?JobOpening;

    public function findAll(): array;

    public function save(JobOpening $jobOpening): void;

    public function delete(string $id): void;
}