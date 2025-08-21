<?php

namespace Modules\Fina\FI\GL\Application;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;

class BalanceCarryForwardService
{
    private $pAndLReportService;
    private $balanceSheetReportService;
    private $postJournalDocumentService;

    public function __construct(
        PAndLReportService $pAndLReportService,
        BalanceSheetReportService $balanceSheetReportService,
        PostJournalDocumentService $postJournalDocumentService
    ) {
        $this->pAndLReportService = $pAndLReportService;
        $this->balanceSheetReportService = $balanceSheetReportService;
        $this->postJournalDocumentService = $postJournalDocumentService;
    }

    public function handle(int $companyCodeId, int $fiscalYearToClose)
    {
        // TODO: A real implementation would use the Fiscal Year Variant to get dates.
        $startDate = Carbon::create($fiscalYearToClose, 1, 1)->startOfDay();
        $endDate = Carbon::create($fiscalYearToClose, 12, 31)->endOfDay();
        $openingDate = $endDate->copy()->addDay();

        $chartOfAccountsId = DB::table('fina_company_codes')->where('id', $companyCodeId)->value('chart_of_accounts_id');
        $coa = ChartOfAccount::find($chartOfAccountsId);
        $retainedEarningsAccountId = $coa->retained_earnings_gl_account_id;

        if (!$retainedEarningsAccountId) {
            throw new Exception("Retained Earnings account is not configured for this Chart of Accounts.");
        }

        return DB::transaction(function () use ($companyCodeId, $startDate, $endDate, $openingDate, $retainedEarningsAccountId) {
            // 1. Close out P&L accounts to Retained Earnings
            $pAndLData = $this->pAndLReportService->handle($companyCodeId, $startDate, $endDate);
            $closingItems = [];
            foreach ($pAndLData['data'] as $account) {
                if (abs($account['net_balance']) > 0.001) {
                    $closingItems[] = [
                        'gl_account_id' => $account['gl_account_id'],
                        'posting_type' => $account['net_balance'] > 0 ? 'Debit' : 'Credit', // Reverse the balance
                        'amount_local_currency' => abs($account['net_balance']),
                        'item_text' => 'Closing entry for FY' . $startDate->year,
                    ];
                }
            }

            if (abs($pAndLData['net_income']) > 0.001) {
                $closingItems[] = [
                    'gl_account_id' => $retainedEarningsAccountId,
                    'posting_type' => $pAndLData['net_income'] > 0 ? 'Credit' : 'Debit',
                    'amount_local_currency' => abs($pAndLData['net_income']),
                    'item_text' => 'Net Income for FY' . $startDate->year,
                ];
            }

            if (!empty($closingItems)) {
                ($this->postJournalDocumentService)([
                    'company_code_id' => $companyCodeId,
                    'document_date' => $endDate,
                    'posting_date' => $endDate,
                    'document_type' => 'CL', // Closing
                    'header_text' => 'Year End Closing ' . $startDate->year,
                    'items' => $closingItems,
                ]);
            }

            // 2. Post opening balances for B/S accounts
            $bsData = $this->balanceSheetReportService->handle($companyCodeId, $endDate);
            $openingItems = [];
            foreach ($bsData['data'] as $account) {
                if (abs($account['balance']) > 0.001) {
                    $openingItems[] = [
                        'gl_account_id' => $account['gl_account_id'],
                        'posting_type' => $account['balance'] > 0 ? 'Debit' : 'Credit',
                        'amount_local_currency' => abs($account['balance']),
                        'item_text' => 'Opening Balance for FY' . $openingDate->year,
                    ];
                }
            }

            if (!empty($openingItems)) {
                ($this->postJournalDocumentService)([
                    'company_code_id' => $companyCodeId,
                    'document_date' => $openingDate,
                    'posting_date' => $openingDate,
                    'document_type' => 'OB', // Opening Balance
                    'header_text' => 'Opening Balances ' . $openingDate->year,
                    'items' => $openingItems,
                ]);
            }

            return ['status' => 'success', 'message' => "Fiscal year $fiscalYearToClose has been closed."];
        });
    }
}
