<?php

namespace Modules\GRC\ComplianceMgt\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\GRC\ComplianceMgt\Domain\ConsentRepositoryInterface;
use Modules\GRC\ComplianceMgt\Domain\Model\Consent;

class EloquentConsentRepository implements ConsentRepositoryInterface
{
    public function findById(int $id): ?Consent
    {
        return Consent::find($id);
    }

    public function getAll(): Collection
    {
        return Consent::all();
    }

    public function save(Consent $consent): Consent
    {
        $consent->save();
        return $consent;
    }

    public function delete(Consent $consent): bool
    {
        return $consent->delete();
    }
}