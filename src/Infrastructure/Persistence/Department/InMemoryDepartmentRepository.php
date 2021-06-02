<?php
declare (strict_types = 1);

namespace App\Infrastructure\Persistence\Department;

use App\Domain\Department\Department;
use App\Domain\Department\DepartmentRepository;
use Illuminate\Database\Capsule\Manager as DB;

class InMemoryDepartmentRepository implements DepartmentRepository
{
    /**
     * @var Department[]
     */
    private $m_arrobjDepartments;

    /**
     * InMemoryDepartmentRepository constructor.
     *
     * @param array|null $m_arrobjDepartments
     */
    public function __construct()
    {
        $this->m_arrobjDepartments = [];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $strSql = 'SELECT
                    *
                FROM departments
                ORDER BY id';

        $arrstdDepartments = DB::select($strSql);

        foreach ($arrstdDepartments as $stdDepartment) {
            $arrstrDepartment            = json_decode(json_encode($stdDepartment), true);
            $this->m_arrobjDepartments[] = new Department($arrstrDepartment);
        }

        return array_values($this->m_arrobjDepartments);
    }
}