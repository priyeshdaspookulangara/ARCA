<?php

namespace Modules\Fina\Tests\Feature;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GLDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_gl_document()
    {
        $data = [
            'company_code_id' => 1,
            'document_date' => '2023-10-26',
            'posting_date' => '2023-10-26',
            'document_type' => 'SA',
            'transaction_currency_code' => 'USD',
            'created_by_user_id' => 1,
            'items' => [
                [
                    'gl_account_id' => 1,
                    'posting_type' => 'Debit',
                    'amount_transaction_currency' => 100,
                    'amount_local_currency' => 100,
                ],
                [
                    'gl_account_id' => 2,
                    'posting_type' => 'Credit',
                    'amount_transaction_currency' => 100,
                    'amount_local_currency' => 100,
                ],
            ],
        ];

        $response = $this->postJson('/api/fina/gl/documents', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'company_code_id',
                'document_number',
                'fiscal_year',
                'document_type',
                'document_date',
                'posting_date',
                'reference_text',
                'header_text',
                'transaction_currency_code',
                'created_by_user_id',
                'created_at',
                'updated_at',
                'items' => [
                    '*' => [
                        'id',
                        'document_header_id',
                        'item_number',
                        'gl_account_id',
                        'posting_type',
                        'amount_transaction_currency',
                        'amount_local_currency',
                        'tax_code_id',
                        'tax_amount_local_currency',
                        'cost_center_id',
                        'internal_order_id',
                        'profit_center_id',
                        'assignment_text',
                        'item_text',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }
}
