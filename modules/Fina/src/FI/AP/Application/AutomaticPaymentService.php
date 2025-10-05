<?php

namespace Modules\Fina\FI\AP\Application;

use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\AP\Domain\Repositories\APInvoiceRepositoryInterface;
use Modules\Fina\FI\AP\Domain\Repositories\PaymentRunRepository;
use Modules\Fina\FI\AP\Domain\Repositories\PaymentProposalRepository;
use Modules\Fina\FI\AP\Domain\Entities\PaymentRun;

class AutomaticPaymentService
{
    private APInvoiceRepositoryInterface $apInvoiceRepository;
    private PaymentRunRepository $paymentRunRepository;
    private PaymentProposalRepository $paymentProposalRepository;

    public function __construct(
        APInvoiceRepositoryInterface $apInvoiceRepository,
        PaymentRunRepository $paymentRunRepository,
        PaymentProposalRepository $paymentProposalRepository
    ) {
        $this->apInvoiceRepository = $apInvoiceRepository;
        $this->paymentRunRepository = $paymentRunRepository;
        $this->paymentProposalRepository = $paymentProposalRepository;
    }

    public function createPaymentProposal(array $parameters): PaymentRun
    {
        return DB::transaction(function () use ($parameters) {
            $paymentRun = new PaymentRun([
                'run_date' => $parameters['run_date'],
                'status' => 'Proposal Created',
                'parameters' => $parameters,
            ]);
            $this->paymentRunRepository->save($paymentRun);

            $openInvoices = $this->apInvoiceRepository->findOpenInvoices(['due_date' => $parameters['due_date']]);

            foreach ($openInvoices as $invoice) {
                $proposal = new \Modules\Fina\FI\AP\Domain\Entities\PaymentProposal([
                    'payment_run_id' => $paymentRun->id,
                    'invoice_id' => $invoice->id,
                    'status' => 'Proposed',
                ]);
                $this->paymentProposalRepository->save($proposal);

                // Lock the invoice
                $invoice->payment_run_id = $paymentRun->id;
                $invoice->save();
            }

            return $paymentRun;
        });
    }

    public function executePaymentRun(int $paymentRunId): void
    {
        DB::transaction(function () use ($paymentRunId) {
            $paymentRun = $this->paymentRunRepository->findById($paymentRunId);
            if (!$paymentRun || $paymentRun->status !== 'Proposal Created') {
                throw new \Exception("Payment run not found or not in a proposable state.");
            }

            $proposals = $this->paymentProposalRepository->getByPaymentRunId($paymentRunId);

            foreach ($proposals as $proposal) {
                if ($proposal->status === 'Proposed') {
                    $proposal->status = 'Paid';
                    $this->paymentProposalRepository->save($proposal);

                    $invoice = $proposal->invoice;
                    $invoice->payment_status = 'Paid';
                    $invoice->save();
                }
            }

            $paymentRun->status = 'Payments Executed';
            $this->paymentRunRepository->save($paymentRun);
        });
    }

    public function cancelPaymentRun(int $paymentRunId): void
    {
        DB::transaction(function () use ($paymentRunId) {
            $paymentRun = $this->paymentRunRepository->findById($paymentRunId);
            if (!$paymentRun) {
                throw new \Exception("Payment run not found.");
            }

            $proposals = $this->paymentProposalRepository->getByPaymentRunId($paymentRunId);

            foreach ($proposals as $proposal) {
                $invoice = $proposal->invoice;
                $invoice->payment_run_id = null;
                $invoice->save();
                $this->paymentProposalRepository->delete($proposal);
            }

            $this->paymentRunRepository->delete($paymentRun);
        });
    }
}