<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\AR\Domain\Entities\ARInvoiceHeader;
use Modules\Fina\FI\AR\Domain\Entities\ARCustomerFinancials;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;

class ARInvoiceHeaderFactory extends Factory
{
    protected $model = ARInvoiceHeader::class;

    public function definition()
    {
        $customerFinancials = ARCustomerFinancials::factory()->create();
        $grossAmount = $this->faker->randomFloat(2, 100, 10000);
        $taxAmount = $grossAmount * 0.1; // Example tax calculation
        $netAmount = $grossAmount - $taxAmount;

        return [
            'gl_document_header_id' => GLDocumentHeader::factory(),
            'customer_id' => $customerFinancials->customer_id,
            'invoice_number_customer' => $this->faker->unique()->numerify('CUST-INV-#####'),
            'invoice_date' => $this->faker->date(),
            'due_date' => $this->faker->dateTimeBetween('-90 days', '-1 day')->format('Y-m-d'),
            'gross_amount' => $grossAmount,
            'net_amount' => $netAmount,
            'tax_amount' => $taxAmount,
            'payment_status' => 'Open',
            'so_number' => $this->faker->numerify('SO-#####'),
        ];
    }
}