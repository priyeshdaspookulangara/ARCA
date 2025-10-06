<?php

namespace Modules\HR\Recruitment\Domain\Repositories;

use Modules\HR\Recruitment\Domain\Entities\Applicant;

interface ApplicantRepositoryInterface
{
    public function findById(string $id): ?Applicant;

    public function findByEmail(string $email): ?Applicant;

    public function save(Applicant $applicant): void;
}