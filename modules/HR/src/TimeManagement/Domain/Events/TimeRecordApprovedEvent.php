<?php

namespace Modules\HR\TimeManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\TimeManagement\Domain\Entities\TimeRecord;

class TimeRecordApprovedEvent
{
    use Dispatchable, SerializesModels;

    public $timeRecord;

    /**
     * Create a new event instance.
     *
     * @param TimeRecord $timeRecord
     */
    public function __construct(TimeRecord $timeRecord)
    {
        $this->timeRecord = $timeRecord;
    }
}