<?php

namespace Modules\POS\Events;

use Illuminate\Queue\SerializesModels;
use Modules\POS\Models\OfflineTransaction;

class OfflineTransactionStored
{
    use SerializesModels;

    public $offlineTransaction;
    public $userId;
    public $shiftId;
    public $terminalId;

    public function __construct(OfflineTransaction $offlineTransaction, $userId, $shiftId, $terminalId)
    {
        $this->offlineTransaction = $offlineTransaction;
        $this->userId = $userId;
        $this->shiftId = $shiftId;
        $this->terminalId = $terminalId;
    }
}
