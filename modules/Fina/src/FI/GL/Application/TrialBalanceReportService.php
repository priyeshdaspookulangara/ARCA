<?php

namespace Modules\Fina\FI\GL\Application;

use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentItem;
use Carbon\Carbon;

class TrialBalanceReportService
{
    public function handle(int $companyCodeId, Carbon $fromDate, Carbon $toDate): array
    {
        $items = GLDocumentItem::query()
            ->join('fina_gl_document_headers', 'fina_gl_document_items.document_header_id', '=', 'fina_gl_document_headers.id')
            ->where('fina_gl_document_headers.company_code_id', $companyCodeId)
            ->whereBetween('fina_gl_document_headers.posting_date', [$fromDate, $toDate])
            ->select(
                'fina_gl_document_items.gl_account_id',
                DB::raw("SUM(CASE WHEN fina_gl_document_items.posting_type = 'Debit' THEN fina_gl_document_items.amount_local_currency ELSE 0 END) as total_debit"),
                DB::raw("SUM(CASE WHEN fina_gl_document_items.posting_type = 'Credit' THEN fina_gl_document_items.amount_local_currency ELSE 0 END) as total_credit")
            )
            ->groupBy('fina_gl_document_items.gl_account_id')
            ->with('glAccount:id,account_number,name') // Eager load GL account info
            ->get();

        $totalDebits = $items->sum('total_debit');
        $totalCredits = $items->sum('total_credit');

        return [
            'report_name' => 'Trial Balance',
            'company_code_id' => $companyCodeId,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'data' => $items,
            'totals' => [
                'debit' => $totalDebits,
                'credit' => $totalCredits,
                'balanced' => abs($totalDebits - $totalCredits) < 0.001 // Check for balance with a small tolerance
            ]
        ];
    }
}
