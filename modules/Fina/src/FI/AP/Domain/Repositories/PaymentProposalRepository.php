<?php

namespace Modules\Fina\FI\AP\Domain\Repositories;

use Modules\Fina\FI\AP\Domain\Entities\PaymentProposal;
use Illuminate\Support\Collection;

interface PaymentProposalRepository
{
    public function findById(int $id): ?PaymentProposal;

    public function getByPaymentRunId(int $paymentRunId): Collection;

    public function save(PaymentProposal $paymentProposal): void;

    public function delete(PaymentProposal $paymentProposal): void;
}