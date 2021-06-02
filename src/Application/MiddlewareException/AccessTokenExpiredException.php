<?php
declare(strict_types=1);

namespace App\Application\MiddlewareException;
use App\Application\Actions\ActionError;

class AccessTokenExpiredException extends MiddlewareException
{
    public $message         = 'Access token is expired.';
    
    public $m_intStatusCode = 401;
    public $m_strType       = ActionError::UNAUTHENTICATED;
}
