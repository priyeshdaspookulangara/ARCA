<?php

namespace Modules\HR\TimeManagement\Application\Services;

use DateTime;
use Modules\HR\TimeManagement\Domain\Entities\TimeRecord;
use Modules\HR\TimeManagement\Domain\Repositories\TimeRecordRepositoryInterface;
use Modules\HR\TimeManagement\Domain\Events\TimeRecordApprovedEvent;
use Illuminate\Contracts\Events\Dispatcher;

class TimeRecordService
{
    private $timeRecordRepository;
    private $eventDispatcher;

    public function __construct(TimeRecordRepositoryInterface $timeRecordRepository, Dispatcher $eventDispatcher)
    {
        $this->timeRecordRepository = $timeRecordRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function recordTime(string $employeeId, string $date, float $hours): TimeRecord
    {
        $id = uniqid('tr_');
        $timeRecord = new TimeRecord($id, $employeeId, new DateTime($date), $hours);
        $this->timeRecordRepository->save($timeRecord);
        return $timeRecord;
    }

    public function approveTimeRecord(string $timeRecordId): ?TimeRecord
    {
        $timeRecord = $this->timeRecordRepository->findById($timeRecordId);
        if ($timeRecord) {
            $timeRecord->approve();
            $this->timeRecordRepository->save($timeRecord);
            $this->eventDispatcher->dispatch(new TimeRecordApprovedEvent($timeRecord));
        }
        return $timeRecord;
    }

    public function getTimeRecordsForEmployee(string $employeeId): array
    {
        return $this->timeRecordRepository->findByEmployee($employeeId);
    }
}