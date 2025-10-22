<?php

namespace Modules\Payments\Listeners;

use Modules\Payments\Events\PaymentCompleted;
use Modules\Payments\Services\JournalBridgeService;

class CreateJournalEntry
{
    protected $journalBridgeService;

    public function __construct(JournalBridgeService $journalBridgeService)
    {
        $this->journalBridgeService = $journalBridgeService;
    }

    public function handle(PaymentCompleted $event)
    {
        // Logic to create a journal entry using the JournalBridgeService
        // $this->journalBridgeService->createJournalEntry($event->transaction);
    }
}
