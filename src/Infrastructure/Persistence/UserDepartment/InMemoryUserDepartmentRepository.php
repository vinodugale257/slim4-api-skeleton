<?php
declare (strict_types = 1);

namespace App\Infrastructure\Persistence\UserDepartment;

use App\Domain\UserDepartment\UserDepartment;
use App\Domain\UserDepartment\UserDepartmentRepository;
use Illuminate\Database\Capsule\Manager as DB;

class InMemoryUserDepartmentRepository implements UserDepartmentRepository
{
    /**
     * @var UserDepartment[]
     */
    private $m_arrobjUserDepartments;

    /**
     * InMemoryUserDepartmentRepository constructor.
     *
     * @param array|null $m_arrobjUserDepartments
     */
    public function __construct()
    {
        $this->m_arrobjUserDepartments = [];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $strSql = 'SELECT
                    *
                FROM user_departments
                ORDER BY id';

        $arrstdUserDepartments = DB::select($strSql);

        foreach ($arrstdUserDepartments as $stdUserDepartment) {
            $arrstrUserDepartment            = json_decode(json_encode($stdUserDepartment), true);
            $this->m_arrobjUserDepartments[] = new UserDepartment($arrstrUserDepartment);
        }

        return array_values($this->m_arrobjUserDepartments);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserDepartmentsOfId(int $userId): array
    {
        $strSql = 'SELECT
                        *
                    FROM
                        user_departments ud
                    WHERE
                        ud.user_id=' . (int) $userId;
        $arrstdUserDepartments = DB::select($strSql);

        foreach ($arrstdUserDepartments as $stdUserDepartment) {
            $arrstrUserDepartment            = json_decode(json_encode($stdUserDepartment), true);
            $this->m_arrobjUserDepartments[] = new UserDepartment($arrstrUserDepartment);
        }

        return array_values($this->m_arrobjUserDepartments);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserDepartmentByUserIdBydepartmentId(int $userId, $intDepartmentId): UserDepartment
    {
        $strSql = 'SELECT
                        *
                    FROM
                        user_departments ud
                    WHERE
                        ud.user_id=' . (int) $userId . '
                        AND ud.department_id=' . (int) $intDepartmentId . '';

        $arrstdUserDepartments = DB::select($strSql);

        if (!valArr($arrstdUserDepartments, 1, true)) {
            throw new UserDepartmentNotFoundException();
        }

        $arrstrUserDepartment = json_decode(json_encode($arrstdUserDepartments[0]), true);

        $objUserDepartment = new UserDepartment();
        $objUserDepartment->fill($arrstrUserDepartment);

        return $objUserDepartment;
    }

}