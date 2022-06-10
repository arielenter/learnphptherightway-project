<?php

declare(strict_types=1);

namespace App\Models;

use App\Model;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileOpenFailException;
use App\Models\TransactionsExtractors\SampleFormat;
use App\Interfaces\TransactionsExtractor;

class TransactionsFile extends Model {

    public function __construct(
            private string $filePath,
            private ?TransactionsExtractor $transactionExtractor = null
    ) {
        parent::__construct();
        $this->transactionExtractor ??= new SampleFormat();
    }

    public function getFileContent() {
        if (($fileContent = fopen($this->filePath, 'r')) !== false) {
            return $fileContent;
        }
        if (!file_exists($this->filePath)) {
            throw new FileNotFoundException();
        }
        throw new FileOpenFailException();
    }

    public function getRawRows(): array {
        $fileContent = $this->getFileContent();
        $rawRows     = [];
        while (($row         = fgetcsv($fileContent)) !== false) {
            $rawRows[] = $row;
        }
        return $rawRows;
    }

    public function extractTransactions(): array {
        $rawRows = $this->getRawRows();
        return $this->transactionExtractor->extract($rawRows);
    }

    public function saveTransacionsInDB(): void {
        $transactions = $this->extractTransactions();
        try {

            $this->db->beginTransaction();
            $this->db->query('DELETE FROM transactions');
            $stmt = $this->db->prepare(
                    'INSERT INTO transactions (date, check_number, description, amount) '
                    . 'VALUES (:date, :check_number, :description, :amount)'
            );
            $stmt->bindParam('date', $date);
            $stmt->bindParam('check_number', $checkNumber, \PDO::PARAM_INT);
            $stmt->bindParam('description', $description);
            $stmt->bindParam('amount', $amount);
            foreach ($transactions as $transaction) {
                $date        = $transaction['date'];
                $checkNumber = $transaction['checkNumber'];
                $description = $transaction['description'];
                $amount      = $transaction['amount'];
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
