<?php

namespace App\Models;

class transactionsTotals {

    private float $netTotal     = 0;
    private float $totalIncome  = 0;
    private float $totalExpense = 0;

    public function __construct(array $transactions) {
        foreach ($transactions as $transaction) {
            $this->netTotal += $transaction['amount'];

            if ($transaction['amount'] >= 0) {
                $this->totalIncome += $transaction['amount'];
            } else {
                $this->totalExpense += $transaction['amount'];
            }
        }
    }

    public function getNetTotal() {
        return $this->netTotal;
    }

    public function getTotalIncome() {
        return $this->totalIncome;
    }

    public function getTotalExpense() {
        return $this->totalExpense;
    }

}
