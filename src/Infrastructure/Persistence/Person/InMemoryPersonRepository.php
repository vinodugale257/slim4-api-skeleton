<?php
declare (strict_types = 1);

namespace App\Infrastructure\Persistence\Person;

use App\Domain\Person\Person;
use App\Domain\Person\PersonRepository;
use Illuminate\Database\Capsule\Manager as DB;

class InMemoryPersonRepository implements PersonRepository
{
    private $m_arrobjPersons;

    public function __construct()
    {
        $this->m_arrobjPersons = [];
    }

    public function findPersonByEmailAddress(string $strEmailAddress): array
    {
        $strSql = 'SELECT * FROM persons WHERE email_address= \'' . $strEmailAddress . '\'';

        $arrstdPersons = DB::select($strSql);

        if (!valArr($arrstdPersons)) {
            return [];
        }
        foreach ($arrstdPersons as $stdPerson) {
            $arrstrPerson    = json_decode(json_encode($stdPerson), true);
            $arrObjPersons[] = new Person($arrstrPerson);
        }
        return array_values($arrObjPersons);
    }

    public function findPersonByMobileNumber(string $strMobileNumber): array
    {
        $strSql = 'SELECT * FROM persons WHERE mobile_number= \'' . $strMobileNumber . '\'';

        $arrstdPersons = DB::select($strSql);

        if (!valArr($arrstdPersons)) {
            return [];
        }
        foreach ($arrstdPersons as $stdPerson) {
            $arrstrPerson    = json_decode(json_encode($stdPerson), true);
            $arrObjPersons[] = new Person($arrstrPerson);
        }
        return array_values($arrObjPersons);
    }
}