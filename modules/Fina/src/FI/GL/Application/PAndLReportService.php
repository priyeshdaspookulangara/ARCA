<?php

namespace Modules\Fina\FI\GL\Application;

use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentItem;
use Carbon\Carbon;

class PAndLReportService
{
    public function handle(int $companyCodeId, Carbon $fromDate, Carbon $toDate): array
    {
        $items = GLDocumentItem::query()
            ->join('fina_gl_document_headers', 'fina_gl_document_items.document_header_id', '=', 'fina_gl_document_headers.id')
            ->join('fina_gl_accounts', 'fina_gl_document_items.gl_account_id', '=', 'fina_gl_accounts.id')
            ->where('fina_gl_document_headers.company_code_id', $companyCodeId)
            ->where('fina_gl_accounts.account_type', 'P&L')
            ->whereBetween('fina_gl_document_headers.posting_date', [$fromDate, $toDate])
            ->select(
                'fina_gl_document_items.gl_account_id',
                'fina_gl_accounts.account_number',
                'fina_gl_accounts.name',
                // Net balance: (Credits - Debits) is typical for P&L
                DB::raw("SUM(CASE WHEN fina_gl_document_items.posting_type = 'Credit' THEN fina_gl_document_items.amount_local_currency ELSE -fina_gl_document_items.amount_local_currency END) as net_balance")
            )
            ->groupBy('fina_gl_document_items.gl_account_id', 'fina_gl_accounts.account_number', 'fina_gl_accounts.name')
            ->get();

        $netIncome = $items->sum('net_balance');

        return [
            'report_name' => 'Profit & Loss Statement',
            'company_code_id' => $companyCodeId,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'data' => $items,
            'net_income' => $netIncome,
        ];
    }
}
