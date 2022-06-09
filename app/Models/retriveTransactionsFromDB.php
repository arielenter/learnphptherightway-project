<?php

declare(strict_types = 1);

namespace App\Models;

use App\Model;

class retriveTransactionsFromDB extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function run() {
        return $this->db->query('SELECT date, check_number AS checkNumber, description, amount '
                        . 'FROM transactions'
                )->fetchAll();
    }

}
