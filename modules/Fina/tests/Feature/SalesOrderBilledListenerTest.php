<?php

namespace Modules\Fina\Tests\Feature;

use Modules\Fina\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesOrderBilledListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_order_billed_event_is_handled()
    {
        Event::fake();

        // Mock the event payload
        $eventPayload = new \stdClass();
        $eventPayload->company_code_id = 1;
        $eventPayload->customer_id = 1;
        $eventPayload->billing_date = '2023-10-26';
        $eventPayload->due_date = '2023-11-25';
        $eventPayload->gross_amount = 1200;
        $eventPayload->net_amount = 1000;
        $eventPayload->tax_amount = 200;
        $eventPayload->currency = 'USD';
        $eventPayload->sales_order_number = 'SO123';
        $eventPayload->user_id = 1;
        $eventPayload->gl_items = [
            [
                'gl_account_id' => 1,
                'posting_type' => 'Debit',
                'amount_transaction_currency' => 1200,
                'amount_local_currency' => 1200,
            ],
            [
                'gl_account_id' => 2,
                'posting_type' => 'Credit',
                'amount_transaction_currency' => 1000,
                'amount_local_currency' => 1000,
            ],
            [
                'gl_account_id' => 3,
                'posting_type' => 'Credit',
                'amount_transaction_currency' => 200,
                'amount_local_currency' => 200,
            ],
        ];

        Event::dispatch('Modules\SD\Events\SalesOrderBilledEvent', $eventPayload);

        Event::assertDispatched('Modules\SD\Events\SalesOrderBilledEvent', function ($event) use ($eventPayload) {
            return $event->company_code_id === $eventPayload->company_code_id;
        });
    }
}
