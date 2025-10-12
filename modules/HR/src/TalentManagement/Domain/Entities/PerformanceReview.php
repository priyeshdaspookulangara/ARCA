<?php

namespace Modules\HR\TalentManagement\Domain\Entities;

use DateTime;

class PerformanceReview implements \JsonSerializable
{
    private $id;
    private $employeeId;
    private $reviewPeriodStartDate;
    private $reviewPeriodEndDate;
    private $rating; // e.g., a scale of 1-5
    private $comments;

    public function __construct(string $id, string $employeeId, DateTime $reviewPeriodStartDate, DateTime $reviewPeriodEndDate)
    {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->reviewPeriodStartDate = $reviewPeriodStartDate;
        $this->reviewPeriodEndDate = $reviewPeriodEndDate;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function getReviewPeriodStartDate(): DateTime
    {
        return $this->reviewPeriodStartDate;
    }

    public function getReviewPeriodEndDate(): DateTime
    {
        return $this->reviewPeriodEndDate;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function completeReview(int $rating, string $comments): void
    {
        $this->rating = $rating;
        $this->comments = $comments;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employeeId,
            'review_period_start_date' => $this->reviewPeriodStartDate->format('Y-m-d'),
            'review_period_end_date' => $this->reviewPeriodEndDate->format('Y-m-d'),
            'rating' => $this->rating,
            'comments' => $this->comments,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}