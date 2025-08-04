<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\AP\Domain\Entities\APInvoiceHeader;

class APInvoiceHeaderFactory extends Factory
{
    protected $model = APInvoiceHeader::class;

    public function definition()
    {
        return [
            'gl_document_header_id' => $this->faker->numberBetween(1, 100),
            'vendor_id' => $this->faker->numberBetween(1, 100),
            'invoice_number_vendor' => $this->faker->uuid,
            'invoice_date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'gross_amount' => $this->faker->randomFloat(2, 100, 1000),
            'net_amount' => $this->faker->randomFloat(2, 80, 800),
            'tax_amount' => $this->faker->randomFloat(2, 20, 200),
            'payment_status' => $this->faker->randomElement(['Open', 'Partially Paid', 'Paid']),
            'po_number' => $this->faker->optional()->ean13,
        ];
    }
}
