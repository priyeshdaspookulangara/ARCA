<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;

class GLDocumentHeaderFactory extends Factory
{
    protected $model = GLDocumentHeader::class;

    public function definition()
    {
        return [
            'document_date' => $this->faker->date(),
            'posting_date' => $this->faker->date(),
            'company_code_id' => 1, // Assuming a company code with ID 1 exists
            'fiscal_year_variant_id' => 1, // Assuming a fiscal year variant with ID 1 exists
            'currency_id' => 1, // Assuming a currency with ID 1 exists
            'reference' => $this->faker->word,
            'header_text' => $this->faker->sentence,
        ];
    }
}