<?php

declare (strict_types = 1);

namespace App\Domain\Person;

interface PersonRepository
{

    /**
     * @param string $emailAddress
     * @return Person
     * @throws PersonNotFoundException
     */
    public function findPersonByEmailAddress(string $emailAddress): array;

    /**
     * @param string $mobileNumber
     * @return Person
     * @throws PersonNotFoundException
     */
    public function findPersonByMobileNumber(string $mobileNumber): array;

}