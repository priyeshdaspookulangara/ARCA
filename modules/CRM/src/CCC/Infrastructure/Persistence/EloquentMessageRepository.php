<?php

namespace Modules\CRM\CCC\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\CRM\CCC\Domain\MessageRepositoryInterface;
use Modules\CRM\CCC\Domain\Model\Message;

class EloquentMessageRepository implements MessageRepositoryInterface
{
    public function findById(int $id): ?Message
    {
        return Message::find($id);
    }

    public function getAll(): Collection
    {
        return Message::all();
    }

    public function save(Message $message): Message
    {
        $message->save();
        return $message;
    }

    public function delete(Message $message): bool
    {
        return $message->delete();
    }
}