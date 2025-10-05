<?php

namespace Modules\HR\OrganizationalManagement\Domain\Entities;

class OrganizationalUnit implements \JsonSerializable
{
    private $id;
    private $name;
    private $parentId;

    public function __construct(string $id, string $name, ?string $parentId = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->parentId = $parentId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parentId,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}