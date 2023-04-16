<?php

namespace Tests\Feature;

use App\Http\Controllers\AccountController;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\NoReturn;
use Tests\TestCase;

class AccMovementSummaryTest extends TestCase
{
    private Collection $transactions;
    private AccountController $accController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accController = new AccountController();
        $this->transactions = collect([
            (object) [
                'id' => 1,
                'account_id' => 1,
                'type' => 'credit',
                'amount' => 100,
                'closing_balance' => 1100,
                'created_at' => Carbon::parse('2023-01-01'),
            ],
            (object) [
                'id' => 2,
                'account_id' => 1,
                'type' => 'credit',
                'amount' => 100,
                'closing_balance' => 1200,
                'created_at' => Carbon::parse('2023-01-01'),
            ],
            (object) [
                'id' => 3,
                'account_id' => 1,
                'type' => 'debit',
                'amount' => 100,
                'closing_balance' => 1100,
                'created_at' => Carbon::parse('2023-01-03'),
            ],
            (object) [
                'id' => 4,
                'account_id' => 1,
                'type' => 'credit',
                'amount' => 100,
                'closing_balance' => 1200,
                'created_at' => Carbon::parse('2023-01-03'),
            ],
            (object) [
                'id' => 5,
                'account_id' => 1,
                'type' => 'credit',
                'amount' => 100,
                'closing_balance' => 1300,
                'created_at' => Carbon::parse('2023-01-03'),
            ],
            (object) [
                'id' => 6,
                'account_id' => 1,
                'type' => 'debit',
                'amount' => 100,
                'closing_balance' => 1100,
                'created_at' => Carbon::parse('2023-01-07'),
            ]
        ]);

    }

    #[NoReturn] public function test_GetDailySummary()
    {
        $endDate = Carbon::parse('2023-01-07');
        $startDate = $endDate->copy()->subDays(7);
        $currentDay = $startDate->copy();
        $numberOfDays = $startDate->diffInDays($endDate);
        $dailySummary = [];

        $dailySummary = $this->accController->getDailySummary($numberOfDays, $this->transactions, $currentDay, $dailySummary);

        // Testing transaction for first entry
        $this->assertEquals(0, $dailySummary[0]->total_debits);
        $this->assertEquals(0, $dailySummary[0]->total_credits);
        $this->assertEquals(1000, $dailySummary[0]->opening_balance);
        $this->assertEquals(1000, $dailySummary[0]->closing_balance);

        // Testing a few days in the middle
        $this->assertEquals(0, $dailySummary[1]->total_debits);
        $this->assertEquals(200, $dailySummary[1]->total_credits);
        $this->assertEquals(1000, $dailySummary[1]->opening_balance);
        $this->assertEquals(1200, $dailySummary[1]->closing_balance);

        $this->assertEquals(100, $dailySummary[3]->total_debits);
        $this->assertEquals(200, $dailySummary[3]->total_credits);
        $this->assertEquals(1200, $dailySummary[3]->opening_balance);
        $this->assertEquals(1300, $dailySummary[3]->closing_balance);

        // Testing transaction for last entry
        $this->assertEquals(0, $dailySummary[6]->total_debits);
        $this->assertEquals(0, $dailySummary[6]->total_credits);
        $this->assertEquals(1200, $dailySummary[6]->opening_balance);
        $this->assertEquals(1200, $dailySummary[6]->closing_balance);

    }
}
