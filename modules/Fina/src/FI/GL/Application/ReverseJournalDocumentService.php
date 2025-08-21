<?php

namespace Modules\Fina\FI\GL\Application;

use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;
use Exception;
use Carbon\Carbon;

class ReverseJournalDocumentService
{
    public function handle(GLDocumentHeader $originalDocument, string $reversalReason, Carbon $reversalDate): GLDocumentHeader
    {
        if ($originalDocument->reversed_by_document_id) {
            throw new Exception("Document is already reversed.");
        }

        return DB::transaction(function () use ($originalDocument, $reversalReason, $reversalDate) {
            // 1. Create the reversal document header
            $reversalHeader = GLDocumentHeader::create([
                'company_code_id' => $originalDocument->company_code_id,
                'document_number' => $this->generateNewDocumentNumber($originalDocument->company_code_id), // Placeholder for number generation
                'fiscal_year' => $reversalDate->year,
                'document_type' => 'SA', // Assuming 'SA' for GL document, maybe a specific reversal type 'SB'
                'document_date' => $reversalDate,
                'posting_date' => $reversalDate,
                'reference_text' => 'Reversal of ' . $originalDocument->document_number,
                'header_text' => $originalDocument->header_text,
                'transaction_currency_code' => $originalDocument->transaction_currency_code,
                'created_by_user_id' => auth()->id(), // Assuming auth is set up
                'reversal_reason' => $reversalReason,
                'reversal_date' => $reversalDate,
                'reverses_document_id' => $originalDocument->id,
            ]);

            // 2. Create inverse items for the reversal document
            foreach ($originalDocument->items as $originalItem) {
                $reversalHeader->items()->create([
                    'gl_account_id' => $originalItem->gl_account_id,
                    'posting_type' => $originalItem->posting_type === 'Debit' ? 'Credit' : 'Debit',
                    'amount_transaction_currency' => $originalItem->amount_transaction_currency,
                    'amount_local_currency' => $originalItem->amount_local_currency,
                    'tax_code_id' => $originalItem->tax_code_id,
                    'tax_amount_local_currency' => $originalItem->tax_amount_local_currency,
                    'cost_center_id' => $originalItem->cost_center_id,
                    'internal_order_id' => $originalItem->internal_order_id,
                    'profit_center_id' => $originalItem->profit_center_id,
                    'assignment_text' => $originalItem->assignment_text,
                    'item_text' => 'Reversal of item ' . $originalItem->item_number,
                ]);
            }

            // 3. Update the original document to link to the reversal
            $originalDocument->reversed_by_document_id = $reversalHeader->id;
            $originalDocument->save();

            return $reversalHeader;
        });
    }

    // A real implementation would have a robust number range service
    private function generateNewDocumentNumber(int $companyCodeId): string
    {
        return 'DOC' . time() . rand(100, 999);
    }
}
