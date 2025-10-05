<?php

namespace Modules\Fina\Tests\Feature\TR;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\TR\Domain\BankBalance;
use Modules\Fina\FI\BL\Domain\Entities\BankAccount;

class BankBalanceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_bank_balance()
    {
        $bankAccount = BankAccount::factory()->create();

        $data = [
            'bank_account_id' => $bankAccount->id,
            'balance_date' => '2024-01-01',
            'balance' => 50000.00,
        ];

        $response = $this->postJson('/api/fina/tr/bank-balances', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_tr_bank_balances', $data);
    }

    public function test_can_get_bank_balance()
    {
        $bankBalance = BankBalance::factory()->create();

        $response = $this->getJson("/api/fina/tr/bank-balances/{$bankBalance->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'bank_account_id' => $bankBalance->bank_account_id,
                'balance' => $bankBalance->balance,
            ]);
    }

    public function test_can_get_all_bank_balances()
    {
        BankBalance::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/tr/bank-balances');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_update_bank_balance()
    {
        $bankBalance = BankBalance::factory()->create();

        $data = [
            'balance' => 60000.00,
        ];

        $response = $this->putJson("/api/fina/tr/bank-balances/{$bankBalance->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_tr_bank_balances', $data);
    }

    public function test_can_delete_bank_balance()
    {
        $bankBalance = BankBalance::factory()->create();

        $response = $this->deleteJson("/api/fina/tr/bank-balances/{$bankBalance->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_tr_bank_balances', ['id' => $bankBalance->id]);
    }
}