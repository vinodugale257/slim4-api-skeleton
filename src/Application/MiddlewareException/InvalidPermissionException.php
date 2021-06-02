<?php
declare (strict_types = 1);

namespace App\Application\MiddlewareException;

use App\Application\Actions\ActionError;

class InvalidPermissionException extends MiddlewareException
{
    public $message = 'User dont have permission.';

    public $m_intStatusCode = 403;
    public $m_strType       = ActionError::INVALID_PERMISSIONS;
}