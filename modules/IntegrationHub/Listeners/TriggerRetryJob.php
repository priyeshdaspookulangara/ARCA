<?php

namespace Modules\IntegrationHub\Listeners;

use Modules\IntegrationHub\Events\IntegrationFailed;

class TriggerRetryJob
{
    public function handle(IntegrationFailed $event)
    {
        // Logic to trigger a retry job
    }
}
