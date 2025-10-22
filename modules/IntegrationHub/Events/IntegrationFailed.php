<?php

namespace Modules\IntegrationHub\Events;

use Illuminate\Queue\SerializesModels;
use Modules\IntegrationHub\Models\IntegrationProfile;

class IntegrationFailed
{
    use SerializesModels;

    public $profile;
    public $exception;
    public $logId;

    public function __construct(IntegrationProfile $profile, \Exception $exception, $logId)
    {
        $this->profile = $profile;
        $this->exception = $exception;
        $this->logId = $logId;
    }
}
