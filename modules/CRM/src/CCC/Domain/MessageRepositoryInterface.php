<?php

namespace Modules\CRM\CCC\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\CCC\Domain\Model\Message;

interface MessageRepositoryInterface
{
    public function findById(int $id): ?Message;

    public function getAll(): Collection;

    public function save(Message $message): Message;

    public function delete(Message $message): bool;
}