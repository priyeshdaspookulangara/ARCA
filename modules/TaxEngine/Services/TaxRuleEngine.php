<?php

namespace Modules\TaxEngine\Services;

class TaxRuleEngine
{
    public function evaluate($transaction, $rules)
    {
        // For now, return a dummy tax rate.
        // In a real implementation, this would involve complex rule evaluation.
        return 10.0;
    }
}
