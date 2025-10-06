<?php

namespace Modules\HR\Recruitment\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Recruitment\Domain\Entities\Applicant;
use Modules\HR\Recruitment\Domain\Entities\Application;

class ApplicantHiredEvent
{
    use Dispatchable, SerializesModels;

    public $applicant;
    public $application;

    /**
     * Create a new event instance.
     *
     * @param Applicant $applicant
     * @param Application $application
     */
    public function __construct(Applicant $applicant, Application $application)
    {
        $this->applicant = $applicant;
        $this->application = $application;
    }
}