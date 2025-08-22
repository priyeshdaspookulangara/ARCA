<?php
namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\GL\Domain\Entities\RecurringEntryDocument;
use Carbon\Carbon;

class RecurringEntryDocumentFactory extends Factory
{
    protected $model = RecurringEntryDocument::class;

    public function definition()
    {
        $startDate = Carbon::parse($this->faker->dateTimeThisYear());
        return [
            'company_code_id' => 1, // Assuming company code 1 exists
            'document_type' => 'SA',
            'transaction_currency_code' => 'USD',
            'header_text' => 'Monthly Rent',
            'frequency' => 'MONTHLY',
            'start_date' => $startDate,
            'next_run_date' => $startDate,
        ];
    }
}
