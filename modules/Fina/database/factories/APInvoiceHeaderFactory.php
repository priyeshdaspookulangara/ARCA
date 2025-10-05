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
            'gl_document_header_id' => 1, // Assuming a GL document header with ID 1 exists
            'vendor_id' => 1, // Assuming a vendor with ID 1 exists
            'invoice_number_vendor' => $this->faker->unique()->numerify('INV-#####'),
            'invoice_date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'gross_amount' => $this->faker->randomFloat(2, 100, 10000),
            'net_amount' => $this->faker->randomFloat(2, 80, 8000),
            'tax_amount' => $this->faker->randomFloat(2, 20, 2000),
            'payment_status' => 'Open',
            'po_number' => $this->faker->numerify('PO-#####'),
        ];
    }
}