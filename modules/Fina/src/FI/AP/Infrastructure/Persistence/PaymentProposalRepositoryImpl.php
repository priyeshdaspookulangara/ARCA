<?php

namespace Modules\Fina\FI\AP\Infrastructure\Persistence;

use Modules\Fina\FI\AP\Domain\Entities\PaymentProposal;
use Modules\Fina\FI\AP\Domain\Repositories\PaymentProposalRepository;
use Illuminate\Support\Collection;

class PaymentProposalRepositoryImpl implements PaymentProposalRepository
{
    public function findById(int $id): ?PaymentProposal
    {
        return PaymentProposal::find($id);
    }

    public function getByPaymentRunId(int $paymentRunId): Collection
    {
        return PaymentProposal::where('payment_run_id', $paymentRunId)->get();
    }

    public function save(PaymentProposal $paymentProposal): void
    {
        $paymentProposal->save();
    }

    public function delete(PaymentProposal $paymentProposal): void
    {
        $paymentProposal->delete();
    }
}