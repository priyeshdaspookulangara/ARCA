<?php

namespace Modules\Fina\FI\GL\Application;

use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentItem;
use Carbon\Carbon;

class BalanceSheetReportService
{
    public function handle(int $companyCodeId, Carbon $asOfDate): array
    {
        // We need to get the net balance of all Balance Sheet accounts up to the specified date.
        $accounts = GLDocumentItem::query()
            ->join('fina_gl_document_headers', 'fina_gl_document_items.document_header_id', '=', 'fina_gl_document_headers.id')
            ->join('fina_gl_accounts', 'fina_gl_document_items.gl_account_id', '=', 'fina_gl_accounts.id')
            ->where('fina_gl_document_headers.company_code_id', $companyCodeId)
            ->where('fina_gl_accounts.account_type', 'Balance Sheet')
            ->where('fina_gl_document_headers.posting_date', '<=', $asOfDate)
            ->select(
                'fina_gl_document_items.gl_account_id',
                'fina_gl_accounts.account_number',
                'fina_gl_accounts.name',
                // Net balance: (Debits - Credits) is typical for Assets, (Credits - Debits) for Liabilities/Equity
                // For simplicity, we can just calculate the net effect on the debit side.
                DB::raw("SUM(CASE WHEN fina_gl_document_items.posting_type = 'Debit' THEN fina_gl_document_items.amount_local_currency ELSE -fina_gl_document_items.amount_local_currency END) as balance")
            )
            ->groupBy('fina_gl_document_items.gl_account_id', 'fina_gl_accounts.account_number', 'fina_gl_accounts.name')
            ->get();

        // A real balance sheet would further classify accounts into Assets, Liabilities, and Equity
        // and check if Assets = Liabilities + Equity. This is a simplified version.
        $totalAssets = 0; // Placeholder for more detailed logic
        $totalLiabilitiesAndEquity = 0; // Placeholder

        return [
            'report_name' => 'Balance Sheet',
            'company_code_id' => $companyCodeId,
            'as_of_date' => $asOfDate->toDateString(),
            'data' => $accounts,
            'totals' => [
                'assets' => $totalAssets,
                'liabilities_and_equity' => $totalLiabilitiesAndEquity,
            ]
        ];
    }
}
