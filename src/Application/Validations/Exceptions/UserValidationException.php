<?php

namespace App\Application\Validations\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class UserValidationException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD     => '{{name}} already exists.',
            'email_address'    => '{{name}} already exists.',
            'mobile_number'    => '{{name}} already exists.',
            'current_password' => '{{name}} is not matching.',
        ],
    ];

    public function chooseTemplate()
    {
        return (valStr($this->getParam('strFieldName'))) ? $this->getParam('strFieldName') : 0;
    }
}