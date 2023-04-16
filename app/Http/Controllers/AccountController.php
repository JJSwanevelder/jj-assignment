<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AccountController extends Controller
{
    /**
     * @param Request $request
     * @param Account $account
     * @return InertiaResponse
     */
    public function dailySummary(Request $request, Account $account): InertiaResponse
    {
        $endDate = $request->exists('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::today();
        $startDate = $request->exists('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::today()->subDays(30);
        $currentDay = $startDate->copy();
        $numberOfDays = $startDate->diffInDays($endDate);
        $dailySummary = [];
        $closingBalance = 0;

        // Retrieve the transactions for the account. Use ORM lazy method to handle a large dataset.
        $transactions = $account->transactions()
            ->select('*')
            ->where('account_id', $account->id)
            ->oldest()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->lazy(2000)->collect()->sortBy('id');

        $dailySummary = $this->getDailySummary($numberOfDays, $transactions, $currentDay, $dailySummary);

        return Inertia::render('AccMovementSummary', [
            'user_account' => $account,
            'daily_summary' => $dailySummary,
        ]);
    }

    /**
     * @param int $numberOfDays
     * @param Collection $transactions
     * @param Carbon $currentDay
     * @param array $dailySummary
     * @return array
     */
    public function getDailySummary(int $numberOfDays, Collection $transactions, Carbon $currentDay, array $dailySummary): array
    {
        for ($day = 0; $day < $numberOfDays; $day++) {
            // Filtering collection by day
            $filteredDay = $transactions->filter(function ($filterTransaction) use ($currentDay) {
                return $filterTransaction->created_at->format('Y-m-d') == $currentDay->format('Y-m-d');
            });

            // Check if the Filtered collection have any transactions
            // If empty it means there are no transactions for the day, and we need to build an entry for a day with 0 movement.
            if ($filteredDay->count() == 0) {
                $dailySummaryItem = $this->processNoMovementSummary($transactions, $currentDay);
            } else {
                $dailySummaryItem = $this->processMovementSummary($filteredDay, $currentDay);
            }

            // Adding aggregated results for the day to the summary
            $dailySummary[] = $dailySummaryItem;
            $currentDay->addDay();
        }

        return $dailySummary;
    }

    /**
     * @param Collection $transactions
     * @param Carbon $currentDay
     * @return object
     */
    public function processNoMovementSummary(Collection $transactions, Carbon $currentDay): object
    {
        $closingBalance = 0;

        // Move forward in the collection to get the first transaction to determine what is the closing/opening balance
        $nextTransaction = $transactions->firstWhere(function ($transaction) use ($currentDay) {
            return $transaction->amount && $transaction->created_at > $currentDay;
        });

        $previousTransaction = null;
        // There might be a case where the next few transactions after the last one does not have additional entries.
        // For this we will need to do the same as the above but backwards.
        if (!$nextTransaction) {
            $previousTransaction = $transactions->last(function ($transaction) use ($currentDay) {
                return $transaction->amount && $transaction->created_at < $currentDay;
            });

        }

        if ($nextTransaction) {
            if ($nextTransaction->type == 'credit') {
                $closingBalance = $nextTransaction->closing_balance - $nextTransaction->amount;
            } else {
                $closingBalance = $nextTransaction->closing_balance + $nextTransaction->amount;
            }
        }

        if ($previousTransaction) {
            $closingBalance = $previousTransaction->closing_balance;
        }

        return (object) [
            'day' => $currentDay->format('d-M-y'),
            'total_credits' => 0,
            'total_debits' => 0,
            'opening_balance' => $closingBalance,
            'closing_balance' => $closingBalance,
        ];
    }

    /**
     * @param Collection $filteredDay
     * @param Carbon $currentDay
     * @return object
     */
    public function processMovementSummary(Collection $filteredDay, Carbon $currentDay): object
    {
        // Calculating balances for the day
        $total_credits = $filteredDay->sum(function ($filteredDayTransaction) {
            if ($filteredDayTransaction->type == 'credit') return $filteredDayTransaction->amount;
        });

        $total_debits = $filteredDay->sum(function ($filteredDayTransaction) {
            if ($filteredDayTransaction->type == 'debit') return $filteredDayTransaction->amount;
        });

        $closing_balance = $filteredDay->last()->closing_balance;

        return (object) [
            'day' => $currentDay->format('d-M-y'),
            'total_credits' => $total_credits,
            'total_debits' => $total_debits,
            'opening_balance' => $closing_balance + $total_debits - $total_credits,
            'closing_balance' => $closing_balance,
        ];
    }
}
