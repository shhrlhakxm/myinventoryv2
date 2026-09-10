<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
     public function __construct(string $itemName, int $currentStock, int $requestedQuantity)
    {
        parent::__construct(
            "Cannot remove {$requestedQuantity} units of \"{$itemName}\": only {$currentStock} in stock."
        );
    }
}
