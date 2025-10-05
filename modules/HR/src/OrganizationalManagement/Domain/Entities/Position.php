<?php

namespace Modules\HR\OrganizationalManagement\Domain\Entities;

class Position implements \JsonSerializable
{
    private $id;
    private $jobId;
    private $orgUnitId;

    public function __construct(string $id, string $jobId, string $orgUnitId)
    {
        $this->id = $id;
        $this->jobId = $jobId;
        $this->orgUnitId = $orgUnitId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function getOrgUnitId(): string
    {
        return $this->orgUnitId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'job_id' => $this->jobId,
            'org_unit_id' => $this->orgUnitId,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}