<?php

namespace Modules\POS\Listeners;

use Modules\POS\Events\OfflineTransactionStored;
use Modules\POS\Models\OfflineActivityLog;

class LogOfflineTransaction
{
    public function handle(OfflineTransactionStored $event)
    {
        OfflineActivityLog::create([
            'ShiftID' => $event->shiftId,
            'TerminalID' => $event->terminalId,
            'UserID' => $event->userId,
            'Activity' => 'Stored offline transaction: ' . $event->offlineTransaction->transaction_id,
        ]);
    }
}
