<?php

namespace Modules\CRM\Compliance\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\Compliance\Domain\Model\Consent;

interface ConsentRepositoryInterface
{
    public function findById(int $id): ?Consent;

    public function getAll(): Collection;

    public function save(Consent $consent): Consent;

    public function delete(Consent $consent): bool;
}