<?php
declare(strict_types=1);

namespace App\Application\MiddlewareException;

use Exception;

abstract class MiddlewareException extends Exception
{
    public $m_intStatusCode;
    public $m_strType;
    
    public function getType(): string
    {
        return $this->m_strType;
    }

    public function getStatusCode(): int
    {
        return $this->m_intStatusCode;
    }
}
