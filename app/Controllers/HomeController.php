<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\View;
use App\Models\transactionsFile;

class HomeController
{
    public function index(): View
    {
        return View::make('index');
    }
    public function transactionFileUpload() {
        (new transactionsFile($_FILES['transaction_file']['tmp_name']))->saveTransacionsInDB();
        die(header('Location: /transaction'));
    }
}
