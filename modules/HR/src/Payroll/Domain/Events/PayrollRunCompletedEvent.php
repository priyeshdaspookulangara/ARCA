<?php

namespace Modules\HR\Payroll\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Payroll\Domain\Entities\PayrollRun;

class PayrollRunCompletedEvent
{
    use Dispatchable, SerializesModels;

    public $payrollRun;
    public $paychecks;

    /**
     * Create a new event instance.
     *
     * @param PayrollRun $payrollRun
     * @param array $paychecks
     */
    public function __construct(PayrollRun $payrollRun, array $paychecks)
    {
        $this->payrollRun = $payrollRun;
        $this->paychecks = $paychecks;
    }
}