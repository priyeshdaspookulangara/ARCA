<?php

namespace Modules\Fina\Tests\Feature;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\FI\AP\Domain\Entities\APInvoiceHeader;

class APInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_invoices()
    {
        APInvoiceHeader::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/ap/invoices');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_list_pending_invoices()
    {
        APInvoiceHeader::factory()->create(['payment_status' => 'Open']);
        APInvoiceHeader::factory()->create(['payment_status' => 'Partially Paid']);
        APInvoiceHeader::factory()->create(['payment_status' => 'Paid']);

        $response = $this->getJson('/api/fina/ap/invoices?status=pending');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }
}
