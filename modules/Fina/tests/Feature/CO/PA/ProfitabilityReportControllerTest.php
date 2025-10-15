<?php

namespace Modules\Fina\Tests\Feature\CO\PA;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\CO\PA\Domain\Entities\MarketSegment;
use Modules\Fina\CO\PA\Domain\Entities\ProfitabilityReport;
use Tests\TestCase;

class ProfitabilityReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_profitability_reports()
    {
        ProfitabilityReport::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/co/pa/profitability-reports');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_create_profitability_report()
    {
        $marketSegment = MarketSegment::factory()->create();
        $data = [
            'market_segment_id' => $marketSegment->id,
            'period_start_date' => '2023-01-01',
            'period_end_date' => '2023-01-31',
            'revenue' => 10000,
            'cost_of_sales' => 5000,
            'detailed_costs' => [['name' => 'Marketing', 'amount' => 1000]],
        ];

        $response = $this->postJson('/api/fina/co/pa/profitability-reports', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'gross_profit' => 5000,
                'net_profit' => 4000,
            ]);

        $this->assertDatabaseHas('fina_co_pa_profitability_reports', [
            'market_segment_id' => $marketSegment->id
        ]);
    }

    public function test_can_get_profitability_report_by_id()
    {
        $profitabilityReport = ProfitabilityReport::factory()->create();

        $response = $this->getJson("/api/fina/co/pa/profitability-reports/{$profitabilityReport->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['revenue' => $profitabilityReport->revenue]);
    }

    public function test_can_update_profitability_report()
    {
        $profitabilityReport = ProfitabilityReport::factory()->create();
        $data = ['revenue' => 12000];

        $response = $this->putJson("/api/fina/co/pa/profitability-reports/{$profitabilityReport->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pa_profitability_reports', ['id' => $profitabilityReport->id, 'revenue' => 12000]);
    }

    public function test_can_delete_profitability_report()
    {
        $profitabilityReport = ProfitabilityReport::factory()->create();

        $response = $this->deleteJson("/api/fina/co/pa/profitability-reports/{$profitabilityReport->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_co_pa_profitability_reports', ['id' => $profitabilityReport->id]);
    }

    public function test_can_filter_profitability_reports_by_market_segment()
    {
        $marketSegment1 = MarketSegment::factory()->create();
        $marketSegment2 = MarketSegment::factory()->create();

        ProfitabilityReport::factory()->count(2)->create(['market_segment_id' => $marketSegment1->id]);
        ProfitabilityReport::factory()->count(3)->create(['market_segment_id' => $marketSegment2->id]);

        $response = $this->getJson("/api/fina/co/pa/profitability-reports?market_segment_id={$marketSegment1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }
}