<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\AP\Domain\Entities\PaymentProposal;
use Modules\Fina\FI\AP\Domain\Entities\PaymentRun;
use Modules\Fina\FI\AP\Domain\Entities\APInvoiceHeader;

class PaymentProposalFactory extends Factory
{
    protected $model = PaymentProposal::class;

    public function definition()
    {
        return [
            'payment_run_id' => PaymentRun::factory(),
            'invoice_id' => APInvoiceHeader::factory(),
            'status' => 'Proposed',
        ];
    }
}