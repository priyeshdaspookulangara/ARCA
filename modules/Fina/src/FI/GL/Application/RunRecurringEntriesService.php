<?php

namespace Modules\Fina\FI\GL\Application;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Fina\FI\GL\Domain\Entities\RecurringEntryDocument;

class RunRecurringEntriesService
{
    private $postJournalDocumentService;

    public function __construct(PostJournalDocumentService $postJournalDocumentService)
    {
        $this->postJournalDocumentService = $postJournalDocumentService;
    }

    public function handle(Carbon $runDate): array
    {
        $dueDocuments = RecurringEntryDocument::with('items')
            ->where('next_run_date', '<=', $runDate)
            ->where(function ($query) use ($runDate) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $runDate);
            })
            ->get();

        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($dueDocuments as $doc) {
            DB::beginTransaction();
            try {
                // Prepare data for the real journal document
                $itemsData = $doc->items->map(function ($item) {
                    return [
                        'gl_account_id' => $item->gl_account_id,
                        'posting_type' => $item->posting_type,
                        'amount_transaction_currency' => $item->amount_transaction_currency,
                        'amount_local_currency' => $item->amount_transaction_currency, // Assuming 1:1 for simplicity
                        'item_text' => $item->item_text,
                    ];
                })->toArray();

                $headerData = [
                    'company_code_id' => $doc->company_code_id,
                    'document_date' => $doc->next_run_date,
                    'posting_date' => $doc->next_run_date,
                    'document_type' => $doc->document_type,
                    'transaction_currency_code' => $doc->transaction_currency_code,
                    'header_text' => $doc->header_text . ' - Recurring Entry',
                    'items' => $itemsData,
                ];

                ($this->postJournalDocumentService)($headerData);

                // Update the recurring entry document
                $doc->last_run_date = $doc->next_run_date;
                $doc->next_run_date = $this->calculateNextRunDate($doc->next_run_date, $doc->frequency);
                $doc->save();

                DB::commit();
                $results['success'][] = $doc->id;

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to post recurring entry document {$doc->id}: " . $e->getMessage());
                $results['failed'][] = ['id' => $doc->id, 'reason' => $e->getMessage()];
            }
        }

        return $results;
    }

    private function calculateNextRunDate(Carbon $currentRunDate, string $frequency): Carbon
    {
        switch ($frequency) {
            case 'MONTHLY':
                return $currentRunDate->addMonth();
            case 'QUARTERLY':
                return $currentRunDate->addMonths(3);
            case 'YEARLY':
                return $currentRunDate->addYear();
            default:
                // If frequency is unknown, effectively disable it by pushing it far into the future.
                return Carbon::now()->addYears(100);
        }
    }
}
