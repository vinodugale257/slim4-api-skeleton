<?php

namespace App\Application\Validations\Rules;

use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use Library\Encryption;

class UserValidation extends BaseValidation
{
    public function valEmailAddress($strInput)
    {
        $objRepository = new InMemoryUserRepository();
        $arrobjUsers   = $objRepository->fetchUserByEmailAddress($strInput);

        $boolIsValid = true;

        if (0 === count($arrobjUsers)) {
            return true;
        }

        if (is_null($this->arrstrRequestParameters['id']) && 0 < count($arrobjUsers)) {
            $boolIsValid = false;
        }

        $objUser = current($arrobjUsers);

        if (!is_null($this->arrstrRequestParameters['id']) && $objUser->getAttribute('id') != $this->arrstrRequestParameters['id']) {
            $boolIsValid = false;
        }

        return $boolIsValid;
    }

    public function valMobileNumber($strInput)
    {
        $objRepository = new InMemoryUserRepository();
        $arrobjUsers   = $objRepository->fetchUserByMobileNumber($strInput);

        $boolIsValid = true;

        if (0 === count($arrobjUsers)) {
            return true;
        }

        if (is_null($this->arrstrRequestParameters['id']) && 0 < count($arrobjUsers)) {
            $boolIsValid = false;
        }

        $objUser = current($arrobjUsers);

        if (!is_null($this->arrstrRequestParameters['id']) && $objUser->getAttribute('id') != $this->arrstrRequestParameters['id']) {
            $boolIsValid = false;
        }

        return $boolIsValid;
    }

    public function valCurrentPassword($strInput)
    {

        $objInMemoryUserRepository = new InMemoryUserRepository();
        $objUser                   = $objInMemoryUserRepository->findUserOfId($this->arrstrRequestParameters['id']);

        $objEncryption        = new Encryption;
        $strDecryptedPassword = $objEncryption->decryptText($objUser->getAttribute('password_encrypted'));

        $boolIsValid = true;

        if ($strDecryptedPassword != $strInput) {
            $boolIsValid = false;
        }

        return $boolIsValid;
    }

}