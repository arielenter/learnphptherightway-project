<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\View;
use App\Models\retriveTransactionsFromDB;
use App\Helpers\TransactionFormatingHelper;
use App\Models\TransactionsTotals;

class TransactionController
{
    public function index(): View
    {
        $transactions = (new retriveTransactionsFromDB)->run();
        return View::make(
            'transactions', 
            [
                'transactions' => $transactions,
                'transactionFormatingHelper' => new TransactionFormatingHelper(),
                'transactionsTotals' => new TransactionsTotals($transactions)
            ]
        );
    }
}