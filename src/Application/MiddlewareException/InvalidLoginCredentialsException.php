<?php
declare(strict_types=1);

namespace App\Application\MiddlewareException;
use App\Application\Actions\ActionError;

class InvalidLoginCredentialsException extends MiddlewareException
{
    public $message         = 'User credentials are missing or invalid.';
    
    public $m_intStatusCode = 401;
    public $m_strType       = ActionError::INVALID_CREDENTIALS;
}
