<?php

namespace App\Application\Validations\Rules;

use App\Infrastructure\Persistence\Person\InMemoryPersonRepository;

class MobileNumberValidation extends BaseValidation
{
    public function valMobileNumber($strInput)
    {
        $objRepository = new InMemoryPersonRepository();
        $arrobjPersons = $objRepository->findPersonByMobileNumber($strInput);

        $boolIsValid = true;

        if (0 === count($arrobjPersons)) {
            return true;
        }

        if (is_null($this->arrstrRequestParameters['id']) && 0 < count($arrobjPersons)) {
            $boolIsValid = false;
        }

        $objPerson = current($arrobjPersons);

        if (!is_null($this->arrstrRequestParameters['id']) && $objPerson->getAttribute('person_id') != $this->arrstrRequestParameters['id']) {
            $boolIsValid = false;
        }

        return $boolIsValid;
    }
}