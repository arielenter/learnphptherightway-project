<?php

namespace App\Models\TransactionsExtractors;

use App\Interfaces\TransactionsExtractor;

class SampleFormat implements TransactionsExtractor {

    public function extract(array $rawRows): array {
        /*
         * Assuming the CVS file is impeccable. Example: All rows have the right 
         * amount of columns, first row are column headers, date and number columns 
         * are formatted correctly and are valid, and the file has at least two rows.
         */
        $transactions = [];
        array_shift($rawRows);
        foreach ($rawRows as $rowKey => $row) {
            [
                    $transactions[$rowKey]['date'],
                    $transactions[$rowKey]['checkNumber'],
                    $transactions[$rowKey]['description'],
                    $transactions[$rowKey]['amount'],
                    ] = $row;
            $transactions[$rowKey]['date']   = (
                    \DateTime::createFromFormat('m/d/Y', $transactions[$rowKey]['date'])
                    )->format('Y-m-d');
            $transactions[$rowKey]['amount'] = (float) str_replace(
                            ['$', ','], '', $transactions[$rowKey]['amount']
            );
        }
        return $transactions;
    }

}
