<?php

declare(strict_types = 1);

namespace App\Helpers;

class TransactionFormatingHelper {

    public function formatDollarAmount(float $amount): string {
        $isNegative = $amount < 0;

        return ($isNegative ? '-' : '') . '$' . number_format(abs($amount), 2);
    }

    public function formatDate($date): string {
        return date('M j, Y', strtotime($date));
    }
    
    public function formatCheckNumber(int $checkNumber): string {
        if($checkNumber !== 0){
            return str_pad(strval($checkNumber), 4, "0", STR_PAD_LEFT);
        }
        return '';
    }

}
