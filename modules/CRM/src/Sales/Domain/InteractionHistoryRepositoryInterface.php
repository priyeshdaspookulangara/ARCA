<?php

namespace Modules\CRM\Sales\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\Sales\Domain\Model\InteractionHistory;

interface InteractionHistoryRepositoryInterface
{
    public function findById(int $id): ?InteractionHistory;

    public function getAll(): Collection;

    public function save(InteractionHistory $interactionHistory): InteractionHistory;

    public function delete(InteractionHistory $interactionHistory): bool;
}