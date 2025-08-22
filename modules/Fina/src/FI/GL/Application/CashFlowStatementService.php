<?php

namespace Modules\Fina\FI\GL\Application;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;

class CashFlowStatementService
{
    private $pAndLReportService;

    public function __construct(PAndLReportService $pAndLReportService)
    {
        $this->pAndLReportService = $pAndLReportService;
    }

    public function handle(int $companyCodeId, Carbon $fromDate, Carbon $toDate): array
    {
        // 1. Get Net Income for the period
        $pAndLData = $this->pAndLReportService->handle($companyCodeId, $fromDate, $toDate);
        $netIncome = $pAndLData['net_income'];

        // 2. Get changes in working capital and other balance sheet accounts
        $operatingAccounts = $this->getAccountBalancesByClassification($companyCodeId, ['Accounts Receivable', 'Inventory', 'Accounts Payable'], $fromDate->copy()->subDay(), $toDate);
        $investingAccounts = $this->getAccountBalancesByClassification($companyCodeId, ['Property, Plant, Equipment'], $fromDate->copy()->subDay(), $toDate);
        $financingAccounts = $this->getAccountBalancesByClassification($companyCodeId, ['Long-Term Debt', 'Common Stock'], $fromDate->copy()->subDay(), $toDate);

        // 3. Calculate cash flow from each activity (simplified)
        $cfOperating = $netIncome - ($operatingAccounts['Accounts Receivable']['change'] ?? 0) + ($operatingAccounts['Accounts Payable']['change'] ?? 0);
        $cfInvesting = -($investingAccounts['Property, Plant, Equipment']['change'] ?? 0);
        $cfFinancing = ($financingAccounts['Long-Term Debt']['change'] ?? 0) + ($financingAccounts['Common Stock']['change'] ?? 0);

        $netCashChange = $cfOperating + $cfInvesting + $cfFinancing;

        return [
            'report_name' => 'Cash Flow Statement (Indirect Method)',
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'operating_activities' => [
                'net_income' => $netIncome,
                'adjustments' => $operatingAccounts,
                'total' => $cfOperating,
            ],
            'investing_activities' => [
                'adjustments' => $investingAccounts,
                'total' => $cfInvesting,
            ],
            'financing_activities' => [
                'adjustments' => $financingAccounts,
                'total' => $cfFinancing,
            ],
            'net_cash_change' => $netCashChange,
        ];
    }

    private function getAccountBalancesByClassification(int $companyCodeId, array $classifications, Carbon $startDate, Carbon $endDate): array
    {
        $accounts = GLAccount::whereIn('classification', $classifications)->pluck('id');

        $startBalances = $this->getBalancesAtDate($companyCodeId, $accounts, $startDate);
        $endBalances = $this->getBalancesAtDate($companyCodeId, $accounts, $endDate);

        $results = [];
        foreach ($classifications as $class) {
            $classAccounts = GLAccount::where('classification', $class)->pluck('id');
            $startBalance = $startBalances->whereIn('gl_account_id', $classAccounts)->sum('balance');
            $endBalance = $endBalances->whereIn('gl_account_id', $classAccounts)->sum('balance');
            $results[$class] = [
                'start_balance' => $startBalance,
                'end_balance' => $endBalance,
                'change' => $endBalance - $startBalance,
            ];
        }
        return $results;
    }

    private function getBalancesAtDate(int $companyCodeId, \Illuminate\Support\Collection $accountIds, Carbon $date)
    {
        if($accountIds->isEmpty()){
            return collect();
        }

        return DB::table('fina_gl_document_items')
            ->join('fina_gl_document_headers', 'fina_gl_document_items.document_header_id', '=', 'fina_gl_document_headers.id')
            ->where('fina_gl_document_headers.company_code_id', $companyCodeId)
            ->whereIn('fina_gl_document_items.gl_account_id', $accountIds)
            ->where('fina_gl_document_headers.posting_date', '<=', $date)
            ->select(
                'fina_gl_document_items.gl_account_id',
                DB::raw("SUM(CASE WHEN posting_type = 'Debit' THEN amount_local_currency ELSE -amount_local_currency END) as balance")
            )
            ->groupBy('fina_gl_document_items.gl_account_id')
            ->get();
    }
}
