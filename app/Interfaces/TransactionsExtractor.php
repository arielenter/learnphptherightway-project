<?php

declare(strict_types=1);

namespace App\Interfaces;

interface TransactionsExtractor {
    public function extract(array $rawRows): array;
}
