<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\View;
use App\Models\TransactionsFile;

class HomeController
{
    public function index(): View
    {
        return View::make('index');
    }
    public function transactionFileUpload() {
        (new TransactionsFile($_FILES['transaction_file']['tmp_name']))->saveTransacionsInDB();
        die(header('Location: /transaction'));
    }
}
