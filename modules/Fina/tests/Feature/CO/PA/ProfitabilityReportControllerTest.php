<?php

namespace Modules\Fina\Tests\Feature\CO\PA;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\CO\PA\Domain\MarketSegment;
use Modules\Fina\CO\PA\Domain\ProfitabilityReport;

class ProfitabilityReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_profitability_report()
    {
        $marketSegment = MarketSegment::factory()->create();

        $data = [
            'market_segment_id' => $marketSegment->id,
            'revenue' => 10000.00,
            'cost' => 5000.00,
            'profit' => 5000.00,
            'period' => '2024-01-01',
        ];

        $response = $this->postJson('/api/fina/co/pa/profitability-reports', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pa_profitability_reports', $data);
    }

    public function test_can_get_profitability_report()
    {
        $profitabilityReport = ProfitabilityReport::factory()->create();

        $response = $this->getJson("/api/fina/co/pa/profitability-reports/{$profitabilityReport->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'revenue' => $profitabilityReport->revenue,
                'cost' => $profitabilityReport->cost,
            ]);
    }

    public function test_can_get_all_profitability_reports()
    {
        ProfitabilityReport::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/co/pa/profitability-reports');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_update_profitability_report()
    {
        $profitabilityReport = ProfitabilityReport::factory()->create();

        $data = [
            'revenue' => 12000.00,
            'cost' => 6000.00,
        ];

        $response = $this->putJson("/api/fina/co/pa/profitability-reports/{$profitabilityReport->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pa_profitability_reports', $data);
    }

    public function test_can_delete_profitability_report()
    {
        $profitabilityReport = ProfitabilityReport::factory()->create();

        $response = $this->deleteJson("/api/fina/co/pa/profitability-reports/{$profitabilityReport->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_co_pa_profitability_reports', ['id' => $profitabilityReport->id]);
    }
}