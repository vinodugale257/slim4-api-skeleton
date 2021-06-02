<?php
declare (strict_types = 1);

namespace App\Domain\Department;

interface DepartmentRepository
{
    /**
     * @return Department[]
     */
    public function findAll(): array;

}