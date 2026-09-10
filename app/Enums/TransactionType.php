<?php

namespace App\Enums;

enum TransactionType: string
{
    case In =  'in';
    case Out = 'out';
    case Adjustment = 'adjustment';


    public function label(): string
    {
        return match ($this) {
            self::In => 'Stock In',
            self::Out => 'Stock Out',
            self::Adjustment => 'Adjustment',
        };
    }
}
