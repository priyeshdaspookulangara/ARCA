<?php

namespace Modules\Fina\FI\GL\Application;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;

class RunAccrualReversalsService
{
    private $reversalService;

    public function __construct(ReverseJournalDocumentService $reversalService)
    {
        $this->reversalService = $reversalService;
    }

    public function handle(Carbon $runDate): array
    {
        $dueDocuments = GLDocumentHeader::with('items')
            ->where('is_reversing_entry', true)
            ->where('reverses_on_date', '<=', $runDate)
            ->whereNull('reversed_by_document_id') // Ensure it hasn't been reversed already
            ->get();

        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($dueDocuments as $docToReverse) {
            try {
                // The ReverseJournalDocumentService already runs within a transaction
                $this->reversalService->handle(
                    $docToReverse,
                    'ACCR_REV', // Accrual Reversal reason
                    $docToReverse->reverses_on_date
                );

                // Mark the original document as no longer needing reversal
                $docToReverse->is_reversing_entry = false;
                $docToReverse->save();

                $results['success'][] = $docToReverse->id;

            } catch (\Exception $e) {
                Log::error("Failed to auto-reverse accrual document {$docToReverse->id}: " . $e->getMessage());
                $results['failed'][] = ['id' => $docToReverse->id, 'reason' => $e->getMessage()];
            }
        }

        return $results;
    }
}
