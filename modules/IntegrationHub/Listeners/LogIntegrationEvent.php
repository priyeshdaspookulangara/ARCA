<?php

namespace Modules\IntegrationHub\Listeners;

use Modules\IntegrationHub\Events\IntegrationResponseReceived;
use Modules\IntegrationHub\Events\IntegrationFailed;
use Modules\IntegrationHub\Models\IntegrationLog;

class LogIntegrationEvent
{
    public function handle($event)
    {
        if ($event instanceof IntegrationResponseReceived) {
            IntegrationLog::find($event->logId)->update([
                'status_code' => 'success',
                'response' => json_encode($event->response),
            ]);
        } elseif ($event instanceof IntegrationFailed) {
            IntegrationLog::find($event->logId)->update([
                'status_code' => 'failed',
                'response' => $event->exception->getMessage(),
            ]);
        }
    }
}
