<?php

namespace Modules\HR\Recruitment\Domain\Repositories;

use Modules\HR\Recruitment\Domain\Entities\Application;

interface ApplicationRepositoryInterface
{
    public function findById(string $id): ?Application;

    public function findByJobOpening(string $jobOpeningId): array;

    public function save(Application $application): void;
}