<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\View;
use App\Models\retriveTransactionsFromDB;
use App\Helpers\transactionFormatingHelper;
use App\Models\transactionsTotals;

class TransactionController
{
    public function index(): View
    {
        $transactions = (new retriveTransactionsFromDB)->run();
        return View::make(
            'transactions', 
            [
                'transactions' => $transactions,
                'transactionFormatingHelper' => new transactionFormatingHelper(),
                'transactionsTotals' => new transactionsTotals($transactions)
            ]
        );
    }
}