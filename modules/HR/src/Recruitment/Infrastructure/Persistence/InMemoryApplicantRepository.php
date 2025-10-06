<?php

namespace Modules\HR\Recruitment\Infrastructure\Persistence;

use Modules\HR\Recruitment\Domain\Entities\Applicant;
use Modules\HR\Recruitment\Domain\Repositories\ApplicantRepositoryInterface;

class InMemoryApplicantRepository implements ApplicantRepositoryInterface
{
    private $applicants = [];

    public function findById(string $id): ?Applicant
    {
        return $this->applicants[$id] ?? null;
    }

    public function findByEmail(string $email): ?Applicant
    {
        foreach ($this->applicants as $applicant) {
            if ($applicant->getEmail() === $email) {
                return $applicant;
            }
        }
        return null;
    }

    public function save(Applicant $applicant): void
    {
        $this->applicants[$applicant->getId()] = $applicant;
    }
}