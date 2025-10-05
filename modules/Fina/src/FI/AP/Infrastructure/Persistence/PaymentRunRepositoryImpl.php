<?php

namespace Modules\Fina\FI\AP\Infrastructure\Persistence;

use Modules\Fina\FI\AP\Domain\Entities\PaymentRun;
use Modules\Fina\FI\AP\Domain\Repositories\PaymentRunRepository;
use Illuminate\Support\Collection;

class PaymentRunRepositoryImpl implements PaymentRunRepository
{
    public function findById(int $id): ?PaymentRun
    {
        return PaymentRun::find($id);
    }

    public function getAll(): Collection
    {
        return PaymentRun::all();
    }

    public function save(PaymentRun $paymentRun): void
    {
        $paymentRun->save();
    }

    public function delete(PaymentRun $paymentRun): void
    {
        $paymentRun->delete();
    }
}