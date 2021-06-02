<?php

namespace App\Application\Validations\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class EmailAddressValidationException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD  => '{{name}} already exists.',
            'email_address' => '{{name}} already exists.',
        ],
    ];

    public function chooseTemplate()
    {
        return (valStr($this->getParam('strFieldName'))) ? $this->getParam('strFieldName') : 0;
    }
}