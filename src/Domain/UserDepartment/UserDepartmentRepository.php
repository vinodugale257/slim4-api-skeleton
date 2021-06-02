<?php
declare (strict_types = 1);

namespace App\Domain\UserDepartment;

interface UserDepartmentRepository
{
    /**
     * @return UserDepartment[]
     */
    public function findAll(): array;

    /**
     * @param int $userId
     * @return UserDepartment[]
     * @throws UserDepartmentNotFoundException
     */
    public function findUserDepartmentsOfId(int $userId): array;

}
