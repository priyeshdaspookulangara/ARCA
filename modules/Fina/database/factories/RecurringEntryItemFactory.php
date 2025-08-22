<?php
namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\GL\Domain\Entities\RecurringEntryItem;
use Modules\Fina\FI\GL\Domain\Entities\RecurringEntryDocument;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;

class RecurringEntryItemFactory extends Factory
{
    protected $model = RecurringEntryItem::class;

    public function definition()
    {
        return [
            'recurring_document_id' => RecurringEntryDocument::factory(),
            'gl_account_id' => GLAccount::factory(),
            'posting_type' => $this->faker->randomElement(['Debit', 'Credit']),
            'amount_transaction_currency' => $this->faker->numberBetween(100, 5000),
            'item_text' => $this->faker->sentence,
        ];
    }
}
