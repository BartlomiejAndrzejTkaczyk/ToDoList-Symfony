<?php

namespace App\Utils;

enum PriorityTask : int
{
    case Low = 1;
    case Medium = 2;
    case High = 3;

    public static function fromInt(int $intType): self
    {
        return match ($intType){
            1 => self::Low,
            2 => self::Medium,
            3 => self::High,
        };
    }
}