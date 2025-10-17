<?php

namespace Modules\CRM\CCC\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\CCC\Domain\Model\CommunicationChannel;

interface CommunicationChannelRepositoryInterface
{
    public function findById(int $id): ?CommunicationChannel;

    public function getAll(): Collection;

    public function save(CommunicationChannel $communicationChannel): CommunicationChannel;

    public function delete(CommunicationChannel $communicationChannel): bool;
}