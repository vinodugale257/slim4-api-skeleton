<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\DomainException\DomainRecordNotFoundException;

class UserInvalidCredentialsException extends DomainRecordNotFoundException
{
    public $message = 'User credentials are missing or invalid.';
}
