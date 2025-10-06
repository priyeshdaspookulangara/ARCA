<?php

namespace Modules\Fina\FI\AR\Application;

use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\AR\Domain\Repositories\ARCustomerFinancialsRepositoryInterface;
use Modules\Fina\FI\AR\Domain\Repositories\ARInvoiceRepositoryInterface;
use Modules\Fina\FI\AR\Domain\Repositories\DunningHistoryRepository;
use Modules\Fina\FI\AR\Domain\Entities\DunningHistory;

class DunningService
{
    private ARCustomerFinancialsRepositoryInterface $customerFinancialsRepository;
    private ARInvoiceRepositoryInterface $invoiceRepository;
    private DunningHistoryRepository $dunningHistoryRepository;

    public function __construct(
        ARCustomerFinancialsRepositoryInterface $customerFinancialsRepository,
        ARInvoiceRepositoryInterface $invoiceRepository,
        DunningHistoryRepository $dunningHistoryRepository
    ) {
        $this->customerFinancialsRepository = $customerFinancialsRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->dunningHistoryRepository = $dunningHistoryRepository;
    }

    public function runDunning(\DateTime $runDate): array
    {
        return DB::transaction(function () use ($runDate) {
            $dunnedCustomers = [];
            $customers = $this->customerFinancialsRepository->findAll();

            foreach ($customers as $customer) {
                $overdueInvoices = $this->invoiceRepository->findOverdueInvoicesByCustomerId($customer->customer_id);

                if ($overdueInvoices->isEmpty()) {
                    continue;
                }

                $dunningProcedure = $customer->dunningProcedure;
                if (!$dunningProcedure) {
                    continue;
                }

                $dunningLevels = $dunningProcedure->dunning_levels;
                $nextDunningLevel = $customer->dunning_level + 1;

                if (!isset($dunningLevels[$nextDunningLevel - 1])) {
                    continue; // Max dunning level reached
                }

                $levelDetails = $dunningLevels[$nextDunningLevel - 1];
                $daysInArrears = $levelDetails['days_in_arrears'];

                // Check if the grace period has passed since the last dunning notice
                if ($customer->last_dunned_on) {
                    $lastDunnedDate = new \DateTime($customer->last_dunned_on);
                    $gracePeriod = $levelDetails['grace_period_days'] ?? 7;
                    $nextDunningDate = (clone $lastDunnedDate)->add(new \DateInterval("P{$gracePeriod}D"));
                    if ($runDate < $nextDunningDate) {
                        continue;
                    }
                }

                // Check if the oldest invoice is overdue enough for the next level
                $oldestInvoiceDueDate = new \DateTime($overdueInvoices->min('due_date'));
                if ($oldestInvoiceDueDate->diff($runDate)->days < $daysInArrears) {
                    continue;
                }

                // Generate dunning notice and update customer
                $dunningNoticeContent = "Dunning Level: {$nextDunningLevel} - Please pay your overdue invoices.";
                $dunningHistory = new DunningHistory([
                    'customer_financials_id' => $customer->id,
                    'dunning_date' => $runDate->format('Y-m-d'),
                    'dunning_level' => $nextDunningLevel,
                    'dunning_notice_content' => $dunningNoticeContent,
                ]);
                $this->dunningHistoryRepository->save($dunningHistory);

                $customer->dunning_level = $nextDunningLevel;
                $customer->last_dunned_on = $runDate->format('Y-m-d');
                $customer->save();

                $dunnedCustomers[] = $customer;
            }

            return $dunnedCustomers;
        });
    }
}