<?php

namespace Modules\HR\Recruitment\Domain\Entities;

class Application implements \JsonSerializable
{
    private $id;
    private $jobOpeningId;
    private $applicantId;
    private $status; // e.g., 'received', 'under_review', 'interviewing', 'offered', 'hired', 'rejected'

    public function __construct(string $id, string $jobOpeningId, string $applicantId)
    {
        $this->id = $id;
        $this->jobOpeningId = $jobOpeningId;
        $this->applicantId = $applicantId;
        $this->status = 'received';
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getJobOpeningId(): string
    {
        return $this->jobOpeningId;
    }

    public function getApplicantId(): string
    {
        return $this->applicantId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function updateStatus(string $newStatus): void
    {
        // In a real application, you might have validation logic here
        // to ensure valid status transitions.
        $this->status = $newStatus;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'job_opening_id' => $this->jobOpeningId,
            'applicant_id' => $this->applicantId,
            'status' => $this->status,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}