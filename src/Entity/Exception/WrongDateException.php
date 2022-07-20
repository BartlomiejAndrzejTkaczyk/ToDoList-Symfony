<?php

namespace App\Entity\Exception;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

class WrongDateException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}