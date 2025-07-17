<?php

namespace Modules\Fina\Listeners\HR;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Fina\FI\GL\Application\PostJournalDocumentService;

class PayrollRunCompletedListener implements ShouldQueue
{
    private $postJournalDocumentService;

    public function __construct(PostJournalDocumentService $postJournalDocumentService)
    {
        $this->postJournalDocumentService = $postJournalDocumentService;
    }

    public function handle($event)
    {
        // This is a simplified example. A real implementation would have more logic.
        // The event object ($event) would contain the payroll data.
        $data = [
            'company_code_id' => $event->company_code_id,
            'document_date' => $event->posting_date,
            'posting_date' => $event->posting_date,
            'document_type' => 'PR', // Payroll
            'transaction_currency_code' => $event->currency,
            'created_by_user_id' => $event->user_id,
            'items' => $event->gl_items,
        ];

        ($this->postJournalDocumentService)($data);
    }
}
