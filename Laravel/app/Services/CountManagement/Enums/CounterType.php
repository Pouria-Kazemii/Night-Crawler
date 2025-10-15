<?php
namespace App\Services\CountManagement\Enums;

enum CounterType: string
{
    case SUCCESS = 'success';
    case REPEATED = 'repeated';
    case CHANGED = 'changed';
    case NOT_CHANGED = 'not_changed';
    
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}