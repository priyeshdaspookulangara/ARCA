<?php

namespace Modules\CRM\Sales\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\CRM\Sales\Domain\InteractionHistoryRepositoryInterface;
use Modules\CRM\Sales\Domain\Model\InteractionHistory;

class EloquentInteractionHistoryRepository implements InteractionHistoryRepositoryInterface
{
    public function findById(int $id): ?InteractionHistory
    {
        return InteractionHistory::find($id);
    }

    public function getAll(): Collection
    {
        return InteractionHistory::all();
    }

    public function save(InteractionHistory $interactionHistory): InteractionHistory
    {
        $interactionHistory->save();
        return $interactionHistory;
    }

    public function delete(InteractionHistory $interactionHistory): bool
    {
        return $interactionHistory->delete();
    }
}