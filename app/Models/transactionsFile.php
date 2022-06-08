<?php

namespace App\Models;

use App\Model;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileOpenFailException;
use App\Enums\Format;

class transactionsFile extends Model {

    public function __construct(
            private string $filePath,
            private string $fileFormat = Format::SAMPLE
    ) {
        parent::__construct();
        if (!isset(Format::ALL_FORMATS[$fileFormat])) {
            throw new InvalidArgumentException('Invalid format.');
        }
    }

    public function getFileContent() {
        if (($fileContent = fopen($this->filePath, 'r')) !== false) {
            return $fileContent;
        } else {
            if (!file_exists($this->filePath)) {
                throw new FileNotFoundException();
            } else {
                throw new FileOpenFailException();
            }
        }
    }

    public function getRawRows() {
        $fileContent = $this->getFileContent();
        $rawRows     = [];
        while (($row         = fgetcsv($fileContent)) !== false) {
            $rawRows[] = $row;
        }
        return $rawRows;
    }

    public function extractTransactions() {
        $rawRows = $this->getRawRows();
        switch ($this->fileFormat) {
            case Format::SAMPLE:
                return $this->sampleFormat($rawRows);
            default:
                return $this->sampleFormat($rawRows);
        }
    }

    public function sampleFormat(array $rawRows) {
        /*
         * Assuming the CVS file is impeccable. Example: All rows have the right 
         * amount of columns, first row are column headers, date and number columns 
         * are formatted correctly and are valid, and the file has at least two rows.
         */
        $Transactions = [];
        array_shift($rawRows);
        foreach ($rawRows as $rowKey => $row) {
            [
                    $Transactions[$rowKey]['date'],
                    $Transactions[$rowKey]['checkNumber'],
                    $Transactions[$rowKey]['descrition'],
                    $Transactions[$rowKey]['amount'],
                    ] = $row;
            $Transactions[$rowKey]['date']   = (
                    \DateTime::createFromFormat('m/d/Y', $Transactions[$rowKey])
                    )->format('Y-m-d');
            $Transactions[$rowKey]['amount'] = (float) str_replace(
                            ['$', ','], '', $Transactions[$rowKey]
            );
        }
    }

    public function saveTransacionsInDB() {
        $Transactions = $this->extractTransactions();
        try {
            $this->db->beginTransaction();
            $this->db > query('TRUNCATE transactions');
            $stmt        = $this->db->prepare(
                    'INSERT INTO transactions (date, check_number, description, amount) '
                    . 'VALUES (:date, :check_number, :description, :amount)'
            );
            $transaction = [];
            $stmt->bindParam('date', $transaction['date']);
            $stmt->bindParam('check_number', $transaction['checkNum'], \PDO::PARAM_INT);
            $stmt->bindParam('description', $transaction['description']);
            $stmt->bindParam('amount', $transaction['amount']);
            foreach ($Transactions as $transaction) {
                $stmt->execute();
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

}
