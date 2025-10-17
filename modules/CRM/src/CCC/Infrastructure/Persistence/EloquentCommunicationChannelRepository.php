<?php

namespace Modules\CRM\CCC\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\CRM\CCC\Domain\CommunicationChannelRepositoryInterface;
use Modules\CRM\CCC\Domain\Model\CommunicationChannel;

class EloquentCommunicationChannelRepository implements CommunicationChannelRepositoryInterface
{
    public function findById(int $id): ?CommunicationChannel
    {
        return CommunicationChannel::find($id);
    }

    public function getAll(): Collection
    {
        return CommunicationChannel::all();
    }

    public function save(CommunicationChannel $communicationChannel): CommunicationChannel
    {
        $communicationChannel->save();
        return $communicationChannel;
    }

    public function delete(CommunicationChannel $communicationChannel): bool
    {
        return $communicationChannel->delete();
    }
}