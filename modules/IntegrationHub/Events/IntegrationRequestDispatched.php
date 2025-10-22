<?php

namespace Modules\IntegrationHub\Events;

use Illuminate\Queue\SerializesModels;
use Modules\IntegrationHub\Models\IntegrationProfile;

class IntegrationRequestDispatched
{
    use SerializesModels;

    public $profile;
    public $payload;
    public $logId;

    public function __construct(IntegrationProfile $profile, array $payload, $logId)
    {
        $this->profile = $profile;
        $this->payload = $payload;
        $this->logId = $logId;
    }
}
