<?php

namespace Modules\HR\Recruitment\Domain\Entities;

class JobOpening implements \JsonSerializable
{
    private $id;
    private $positionId;
    private $status; // e.g., 'open', 'closed'

    public function __construct(string $id, string $positionId)
    {
        $this->id = $id;
        $this->positionId = $positionId;
        $this->status = 'open';
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPositionId(): string
    {
        return $this->positionId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function close(): void
    {
        $this->status = 'closed';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'position_id' => $this->positionId,
            'status' => $this->status,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}