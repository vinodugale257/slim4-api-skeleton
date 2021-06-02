<?php
declare (strict_types = 1);

namespace App\Infrastructure\Persistence\UserType;

use App\Domain\UserType\UserType;
use App\Domain\UserType\UserTypeRepository;
use Illuminate\Database\Capsule\Manager as DB;

class InMemoryUserTypeRepository implements UserTypeRepository
{
    /**
     * @var UserType[]
     */
    private $m_arrobjUserTypes;

    /**
     * InMemoryUserTypeRepository constructor.
     *
     * @param array|null $m_arrobjUserTypes
     */
    public function __construct()
    {
        $this->m_arrobjUserTypes = [];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $strSql = 'SELECT
                    *
                FROM user_types
                ORDER BY id';

        $arrstdUserTypes = DB::select($strSql);

        foreach ($arrstdUserTypes as $stdUserType) {
            $arrstrUserType            = json_decode(json_encode($stdUserType), true);
            $this->m_arrobjUserTypes[] = new UserType($arrstrUserType);
        }

        return array_values($this->m_arrobjUserTypes);
    }
}