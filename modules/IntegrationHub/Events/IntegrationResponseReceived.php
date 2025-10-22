<?php

namespace Modules\IntegrationHub\Events;

use Illuminate\Queue\SerializesModels;
use Modules\IntegrationHub\Models\IntegrationProfile;

class IntegrationResponseReceived
{
    use SerializesModels;

    public $profile;
    public $response;
    public $logId;

    public function __construct(IntegrationProfile $profile, $response, $logId)
    {
        $this->profile = $profile;
        $this->response = $response;
        $this->logId = $logId;
    }
}
