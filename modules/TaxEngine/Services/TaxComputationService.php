<?php

namespace Modules\TaxEngine\Services;

class TaxComputationService
{
    protected $ruleEngine;

    public function __construct(TaxRuleEngine $ruleEngine)
    {
        $this->ruleEngine = $ruleEngine;
    }

    public function calculate($transaction)
    {
        // In a real implementation, we would fetch the rules from the database.
        $rules = [];

        $taxRate = $this->ruleEngine->evaluate($transaction, $rules);

        return ($transaction['amount'] * $taxRate) / 100;
    }
}
