<?php

namespace App\Helpers;

class transactionFormatingHelper {

    public function formatDollarAmount(float $amount) {
        $isNegative = $amount < 0;

        return ($isNegative ? '-' : '') . '$' . number_format(abs($amount), 2);
    }

    public function formatDate($date) {
        return date('M j, Y', strtotime($date));
    }
    
    public function formatCheckNumber(int $checkNumber) {
        if($checkNumber !== 0){
            return str_pad($checkNumber, 4, "0", STR_PAD_LEFT);
        }
        return '';
    }

}
