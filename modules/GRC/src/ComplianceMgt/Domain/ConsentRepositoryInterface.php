<?php

namespace Modules\GRC\ComplianceMgt\Domain;

use Illuminate\Support\Collection;
use Modules\GRC\ComplianceMgt\Domain\Model\Consent;

interface ConsentRepositoryInterface
{
    public function findById(int $id): ?Consent;

    public function getAll(): Collection;

    public function save(Consent $consent): Consent;

    public function delete(Consent $consent): bool;
}