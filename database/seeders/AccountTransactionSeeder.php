<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AccountTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        /* -----------------------------------------------------
         * 1. | Creating x number of test user accounts.
         * ---------------------------------------------------*/
        $numUsers = env('SEEDER_NUM_TEST_USERS') ?? 5;
        echo("--------- Creating $numUsers users --------- \n");
        User::factory()->count($numUsers)->create()->each(function ($user) {

            /* --------------------------------------------------------------
             * 2. | Creating x number of test transaction accounts per user.
             * ------------------------------------------------------------*/
            $numAccounts = env('SEEDER_NUM_TEST_ACCOUNTS') ?? rand(3,5);
            echo("--------- Creating $numAccounts accounts for each user --------- \n");
            Account::factory()->count($numAccounts)->create(['user_id' => $user->id])->each(function ($account) {

                /* ----------------------------------------------------------------------------------------------
                 * 2.1 | Setting up date ranges that we want to loop over and add transactions for each account.
                 * --------------------------------------------------------------------------------------------*/
                echo("--------- Processing account Id: $account->id ---------\n");
                $numTransactionDays = env('SEEDER_NUM_TRANSACTION_DAYS') ?? 182;
                $dateNow = Carbon::now();
                $startDate = $dateNow->copy()->subDays($numTransactionDays);
                $transactionDateNow = $startDate->copy();
                $numberOfDays = $dateNow->diffInDays($startDate);
                $accStartingBalance = env('SEEDER_ACC_STARTING_BALANCE') ?? rand(1000,2000);
                $percentageOfAccBalance = env('SEEDER_PERCENTAGE_OF_ACC_BALANCE') ?? 0.8;

                /* -----------------------------------------
                 * 3 | Processing x days to add transactions
                 * ----------------------------------------*/
                while ($numberOfDays > 1) {
                    echo("Processing day number: $numberOfDays ($transactionDateNow) for account: $account->id \n");
                    // Logic to ensure not all days have transactions and that we move forward in days.
                    $rand = rand(1, 10);
                    if ($rand > 9) {
                        // 10% chance to skip the current day, and move to a random day in the future.
                        $days = rand(1,3);

                        // Guard clause
                        if ($transactionDateNow > $dateNow) break;

                        $transactionDateNow->addDays($days);
                        $numberOfDays -= $days;
                    }

                    /* ----------------------------------------------------------------------------------
                     * 3.1 | There 75% chance that the transaction is a debit.
                     *
                     * When the transaction is a credit transaction, the amount should be equal to X% of
                     * the account balance at that point in time, rounded to the nearest R1.
                     * --------------------------------------------------------------------------------*/
                    $tranType = rand(0, 3) <= 2 ? 'debit' : 'credit';

                    if ($tranType == 'credit') {
                    $amount = abs(round($accStartingBalance * $percentageOfAccBalance, 2));
                        $accStartingBalance += $amount;
                    } else {
                        $amount = rand(100.50, 500.50);
                        $accStartingBalance -= $amount;
                    }

                    /* --------------------------------------------
                     * 4 | Processing complete - saving transaction
                     * ------------------------------------------*/
                    $transaction = new AccountTransaction([
                        'account_id' => $account->id,
                        'type' => $tranType,
                        'amount' => $amount,
                        'closing_balance' => $accStartingBalance,
                        'created_at' => $transactionDateNow,
                        'updated_at' => $transactionDateNow,
                    ]);

                    $transaction->save();
                    echo("======================================================\n");
                    echo("Transaction details for transaction id $transaction->id\n");
                    echo("======================================================\n");
                    echo("Total amount allocated to account: $amount \n");
                    echo("Transaction type: $tranType\n");
                    echo("Closing balance: $accStartingBalance \n");
                    echo("******* Transaction detail end ******* \n");
                    echo("======================================================\n\n");
                }

                $account->balance = $accStartingBalance;
                $account->save();
            });
        });

        // Updating first user to variable config if set.
        $user = User::query()->where('id', 1)->first();
        if ((env('SEEDER_USER_NAME') &&
                env('SEEDER_USER_EMAIL') &&
                env('SEEDER_USER_PASSWORD')) &&
            $user) {
            $user->name = env('SEEDER_USER_NAME');
            $user->email = env('SEEDER_USER_EMAIL');
            $user->password = env('SEEDER_USER_PASSWORD');
            $user->save();
        }
    }
}
