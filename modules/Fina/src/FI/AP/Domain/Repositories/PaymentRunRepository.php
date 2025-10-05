<?php

namespace Modules\Fina\FI\AP\Domain\Repositories;

use Modules\Fina\FI\AP\Domain\Entities\PaymentRun;
use Illuminate\Support\Collection;

interface PaymentRunRepository
{
    public function findById(int $id): ?PaymentRun;

    public function getAll(): Collection;

    public function save(PaymentRun $paymentRun): void;

    public function delete(PaymentRun $paymentRun): void;
}