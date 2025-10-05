<?php

namespace Modules\Fina\Tests\Feature\FI\AP;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\FI\AP\Domain\Entities\PaymentRun;
use Modules\Fina\FI\AP\Domain\Entities\APInvoiceHeader;
use Modules\Fina\FI\AP\Domain\Entities\PaymentProposal;

class AutomaticPaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_payment_proposal()
    {
        APInvoiceHeader::factory()->create([
            'due_date' => now()->addDays(10),
            'payment_status' => 'Open',
            'payment_block' => null,
            'payment_run_id' => null,
        ]);

        $data = [
            'run_date' => now()->toDateString(),
            'due_date' => now()->addDays(15)->toDateString(),
        ];

        $response = $this->postJson('/api/fina/ap/payment-runs', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['status' => 'Proposal Created']);

        $this->assertDatabaseHas('fina_ap_payment_runs', ['status' => 'Proposal Created']);
        $this->assertDatabaseHas('fina_ap_payment_proposals', ['status' => 'Proposed']);
    }

    public function test_can_get_payment_run()
    {
        $paymentRun = PaymentRun::factory()->create();
        PaymentProposal::factory()->create(['payment_run_id' => $paymentRun->id]);

        $response = $this->getJson("/api/fina/ap/payment-runs/{$paymentRun->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'run_date', 'status', 'parameters', 'proposals' => []]);
    }

    public function test_can_execute_payment_run()
    {
        $paymentRun = PaymentRun::factory()->create();
        $invoice = APInvoiceHeader::factory()->create(['payment_status' => 'Open']);
        PaymentProposal::factory()->create([
            'payment_run_id' => $paymentRun->id,
            'invoice_id' => $invoice->id,
            'status' => 'Proposed'
        ]);

        $response = $this->putJson("/api/fina/ap/payment-runs/{$paymentRun->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Payment run executed successfully.']);

        $this->assertDatabaseHas('fina_ap_payment_runs', [
            'id' => $paymentRun->id,
            'status' => 'Payments Executed',
        ]);
        $this->assertDatabaseHas('fina_ap_invoices_header', [
            'id' => $invoice->id,
            'payment_status' => 'Paid',
        ]);
    }

    public function test_can_cancel_payment_run()
    {
        $paymentRun = PaymentRun::factory()->create();
        $invoice = APInvoiceHeader::factory()->create();
        $proposal = PaymentProposal::factory()->create([
            'payment_run_id' => $paymentRun->id,
            'invoice_id' => $invoice->id,
        ]);

        $response = $this->deleteJson("/api/fina/ap/payment-runs/{$paymentRun->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_ap_payment_runs', ['id' => $paymentRun->id]);
        $this->assertDatabaseMissing('fina_ap_payment_proposals', ['id' => $proposal->id]);
    }
}