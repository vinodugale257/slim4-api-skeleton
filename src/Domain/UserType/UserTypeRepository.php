<?php
declare (strict_types = 1);

namespace App\Domain\UserType;

interface UserTypeRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

}