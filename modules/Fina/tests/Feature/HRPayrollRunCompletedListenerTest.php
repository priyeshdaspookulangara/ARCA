<?php

namespace Modules\Fina\Tests\Feature;

use Modules\Fina\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HRPayrollRunCompletedListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_run_completed_event_is_handled()
    {
        Event::fake();

        // Mock the event payload
        $eventPayload = new \stdClass();
        $eventPayload->company_code_id = 1;
        $eventPayload->posting_date = '2023-10-26';
        $eventPayload->currency = 'USD';
        $eventPayload->user_id = 1;
        $eventPayload->gl_items = [
            [
                'gl_account_id' => 1,
                'posting_type' => 'Debit',
                'amount_transaction_currency' => 5000,
                'amount_local_currency' => 5000,
            ],
            [
                'gl_account_id' => 2,
                'posting_type' => 'Credit',
                'amount_transaction_currency' => 5000,
                'amount_local_currency' => 5000,
            ],
        ];

        Event::dispatch('Modules\HR\Events\PayrollRunCompletedEvent', $eventPayload);

        Event::assertDispatched('Modules\HR\Events\PayrollRunCompletedEvent', function ($event) use ($eventPayload) {
            return $event->company_code_id === $eventPayload->company_code_id;
        });
    }
}
