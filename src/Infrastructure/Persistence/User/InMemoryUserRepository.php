<?php
declare (strict_types = 1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\UserType\UserType;
use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use Illuminate\Database\Capsule\Manager as DB;

class InMemoryUserRepository implements UserRepository
{
    /**
     * @var User[]
     */
    private $m_arrobjUsers;

    /**
     * InMemoryUserRepository constructor.
     *
     * @param array|null $m_arrobjUsers
     */
    public function __construct()
    {
        $this->m_arrobjUsers = [];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->m_arrobjUsers);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(int $id): User
    {
        $strSql = 'SELECT
                        p.id as person_id,
                        p.first_name,
                        p.middle_name,
                        p.last_name,
                        p.mobile_number,
                        p.email_address,
                        ut.name as user_type,
                        u.*
                    FROM
                        users u
                        JOIN user_types ut ON(u.user_type_id = ut.id )
                        LEFT JOIN employees e ON(u.reference_id = e.id )
                        LEFT JOIN persons p on(p.id = e.person_id)
                    WHERE
                        u.id=' . (int) $id;
        $arrstdUsers = DB::select($strSql);

        if (!valArr($arrstdUsers, 1, true)) {
            throw new UserNotFoundException();
        }

        $arrstrUser = json_decode(json_encode($arrstdUsers[0]), true);

        $objUser = new User();
        $objUser->fill($arrstrUser);

        return $objUser;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserInfoByUserTypeIdByRefrenceId($intUserTypeId, $intRefrenceId): User
    {
        $arrstrFields = [
            'r.id as reference_id',
            'p.first_name',
            'p.last_name',
            'p.mobile_number',
            'p.adhaar_number',
            'p.pan_number',
            'p.email_address',
            'p.pin_code',
            'p.address',
            'ut.id as user_type_id',
            'ut.name as user_type',
            'u.id',
            'u.username',
            'u.is_password_changed',
        ];

        $strReferenceTable = '';
        $strAccountStatus  = '';
        $strStates         = '';
        $strDistricts      = '';
        $strTalukas        = '';

        switch ($intUserTypeId) {
            case UserType::PATIENT:
                $strReferenceTable = 'patients';
                $arrstrFields      = array_merge($arrstrFields, ['r.patient_status_id', 's.id as state_id', 's.name as state_name', 'dist.id as district_id', 'dist.name as district_name', 'tal.id as taluka_id', 'tal.name as taluka_name']);
                $strStates         = 'LEFT JOIN states as s ON (s.id = p.state_id)';
                $strDistricts      = 'LEFT JOIN districts as dist ON (dist.id = p.district_id)';
                $strTalukas        = 'LEFT JOIN talukas as tal ON (tal.id = p.taluka_id)';

                break;

            case UserType::ADMIN:
            case UserType::EMPLOYEE:
            default:
                $strReferenceTable = 'employees';
                break;
        }

        $strSql = '
                    SELECT
                        ' . implode(',', $arrstrFields) . '
                    FROM
                        ' . $strReferenceTable . ' r
                        JOIN persons p ON (p.id = r.person_id)
                        JOIN user_types ut ON (ut.id =' . $intUserTypeId . ')
                        LEFT JOIN users u ON (r.id = u.reference_id AND u.user_type_id = ut.id)
                        ' . $strAccountStatus . '
                        ' . $strStates . '
                        ' . $strDistricts . '
                        ' . $strTalukas . '
                    WHERE
                        r.id = ' . (int) $intRefrenceId;

        $arrstdUsers = DB::select($strSql);

        if (!valArr($arrstdUsers, 1, true)) {
            throw new UserNotFoundException();
        }

        $arrstrUser = json_decode(json_encode($arrstdUsers[0]), true);

        $objUser = new User();
        $objUser->fill($arrstrUser);

        return $objUser;
    }

    public function fetchUserByUserTypeIdByReferenceId(int $intUserTypeId, int $intReferenceId)
    {
        $strSql = 'SELECT
                        u.*
                    FROM
                        users u
                    WHERE
                        u.user_type_id=' . (int) $intUserTypeId . '
                        AND u.reference_id=' . (int) $intReferenceId . '
                        AND u.deactivated_on IS NULL
                        AND deleted_on IS NULL';

        // display($strSql);exit;
        $arrstdUsers = DB::select($strSql);
        $arrstrUser  = valArr($arrstdUsers, 1, true) ? json_decode(json_encode($arrstdUsers[0]), true) : null;

        if (valArr($arrstrUser)) {
            $objUser = new User();
            $objUser->fill($arrstrUser);
            return $objUser;
        } else {
            return null;
        }
    }

    public function fetchUserByUsername(string $strUsername): User
    {
        $strSql = 'SELECT
                        *
                    FROM (
                        SELECT
                            u.*
                        FROM
                            users u
                            JOIN employees e ON( e.id = u.reference_id AND u.user_type_id IN( ' . UserType::ADMIN . ', ' . UserType::EMPLOYEE . ' ) )
                            JOIN persons p on(p.id = e.person_id)
                        WHERE
                            u.deactivated_on IS NULL
                            AND u.deleted_on IS NULL
                            AND u.username=\'' . $strUsername . '\'
                        UNION
                        SELECT
                            u.*
                        FROM
                            users u
                            JOIN employees e ON( e.id = u.reference_id AND u.user_type_id = ' . UserType::PATIENT . ' )
                            JOIN persons p on(p.id = e.person_id)
                            JOIN patients p2 on(p.id = p2.person_id)
                        WHERE
                            u.deactivated_on IS NULL
                            AND u.deleted_on IS NULL
                            AND u.username=\'' . $strUsername . '\'
                    ) as sub
                    ORDER BY
                        user_type_id';

        $arrstdUsers = DB::select($strSql);

        if (!valArr($arrstdUsers, 1, true)) {
            return null;
        }

        $arrstrUser = json_decode(json_encode($arrstdUsers[0]), true);

        $objUser = new User();
        $objUser->fill($arrstrUser);

        return $objUser;
    }

    public function fetchRoutepermissionByUserTypeIdByReferenceId($intuserTypeId, $intReferenceId, $strRouteName)
    {
        $strSql = 'SELECT
                        u2.username,
                        u2.reference_id,
                        u2.user_type_id as user_type,
                        ut.name,
                        d.name as group_name,
                        uda.route_id,
                        a2.name as route_name
                    FROM users u2
                    JOIN user_roles ud ON (u2.id = ud.user_id)
                    JOIN roles d ON (ud.role_id = d.id)
                    LEFT JOIN user_role_routes uda ON (ud.role_id = uda.role_id)
                    JOIN routes a2 ON (uda.route_id = a2.id)
                    JOIN user_types ut ON (u2.user_type_id = ut.id)
                    WHERE u2.user_type_id = ' . $intuserTypeId . ' AND u2.reference_id = ' . $intReferenceId . ' AND a2.name = \'' . $strRouteName . '\'
                    UNION
                        select
                        u2.username,
                        u2.reference_id,
                        u2.user_type_id as user_type,
                        ut.name as group_name,
                        d.name,
                        ua.route_id,
                        a2.name as route_name
                    FROM users u2
                    JOIN user_roles ud on (u2.id = ud.user_id)
                    JOIN roles d on (ud.role_id = d.id)
                    LEFT JOIN user_routes ua on (u2.id = ua.user_id)
                    JOIN routes a2 on (ua.route_id = a2.id)
                    JOIN user_types ut on (u2.user_type_id = ut.id)
                    WHERE u2.user_type_id = ' . $intuserTypeId . ' AND u2.reference_id = ' . $intReferenceId . ' AND a2.name = \'' . $strRouteName . '\'';

        $arrstdUsers = DB::select($strSql);

        $arrstrUser = valArr($arrstdUsers, 1, true) ? json_decode(json_encode($arrstdUsers[0]), true) : null;

        if (valArr($arrstrUser)) {
            $objUser = new User();
            $objUser->fill($arrstrUser);
            return $objUser;
        } else {
            return null;
        }
    }

    public function fetchAllRoutepermissionByUserTypeIdByReferenceId($intuserTypeId, $intReferenceId)
    {
        $strSql = 'SELECT
                            u2.username,
                            u2.reference_id,
                            u2.user_type_id as user_type,
                            ut.name,
                            d.name as group_name,
                            uda.route_id,
                            a2.name as route_name
                        FROM users u2
                        JOIN user_roles ud ON (u2.id = ud.user_id)
                        JOIN roles d ON (ud.role_id = d.id)
                        LEFT JOIN user_role_routes uda ON (ud.role_id = uda.role_id)
                        JOIN routes a2 ON (uda.route_id = a2.id)
                        JOIN user_types ut ON (u2.user_type_id = ut.id)
                        WHERE u2.user_type_id = '.(int)$intuserTypeId.' AND u2.reference_id = '.(int)$intReferenceId.'
                        UNION
                            select
                            u2.username,
                            u2.reference_id,
                            u2.user_type_id as user_type,
                            ut.name as group_name,
                            d.name,
                            ua.route_id,
                            a2.name as route_name
                        FROM users u2
                        JOIN user_roles ud on (u2.id = ud.user_id)
                        JOIN roles d on (ud.role_id = d.id)
                        LEFT JOIN user_routes ua on (u2.id = ua.user_id)
                        JOIN routes a2 on (ua.route_id = a2.id)
                        JOIN user_types ut on (u2.user_type_id = ut.id)
                        WHERE u2.user_type_id ='.(int)$intuserTypeId.' AND u2.reference_id = '.(int)$intReferenceId.'';

        $arrstdUsers = DB::select($strSql);

        foreach ($arrstdUsers as $stdUser) {
            $arrstrUser            = json_decode(json_encode($stdUser), true);
            $this->m_arrobjUsers[] = new User($arrstrUser);
        }

        return array_values($this->m_arrobjUsers);
    }

    public function fetchAllUsersCount($objPaginationParams)
    {

        $arrSearchParams = ['u.deleted_on IS NULL', 'u.deactivated_on IS NULL'];

        $strFirstName = $objPaginationParams->getFirstName() == '' ? '' : array_push($arrSearchParams, 'p.first_name ilike \'%' . $objPaginationParams->getFirstName() . '%\'');
        $strLastName  = $objPaginationParams->getLastName() == '' ? '' : array_push($arrSearchParams, 'p.last_name ilike \'%' . $objPaginationParams->getLastName() . '%\'');
        $strMobile    = $objPaginationParams->getMobile() == '' ? '' : array_push($arrSearchParams, 'p.mobile_number ilike \'%' . $objPaginationParams->getMobile() . '%\'');
        $strUserName  = $objPaginationParams->getUserName() == '' ? '' : array_push($arrSearchParams, 'u.username ilike \'%' . $objPaginationParams->getUserName() . '%\'');
        $strFromDate  = $objPaginationParams->getFromDate() == null ? '' : array_push($arrSearchParams, 'u.created_on::date >= \'' . $objPaginationParams->getFromDate() . '\'::date');
        $strToDate    = $objPaginationParams->getToDate() == null ? '' : array_push($arrSearchParams, 'u.created_on::date <= \'' . $objPaginationParams->getToDate() . '\'::date');

        $strSql = 'SELECT
                    u.id,
                    u.user_type_id,
                    u.reference_id,
                    u.username,
                    p.first_name,
                    p.last_name,
                    p.mobile_number,
                    ut.name as user_type
                FROM users u
                LEFT JOIN user_types ut ON u.user_type_id = ut.id
                JOIN employees e ON (u.reference_id = e.id)
                JOIN persons p on p.id = e.person_id
                WHERE ' . implode(' AND ', $arrSearchParams) . '';

        $arrstdUsers = DB::select($strSql);

        return count($arrstdUsers);
    }

    public function findAllUsersByPageNumberByLimit($objPaginationParams): array
    {

        $arrSearchParams = ['u.deleted_on IS NULL', 'u.deactivated_on IS NULL'];

        $strFirstName = $objPaginationParams->getFirstName() == '' ? '' : array_push($arrSearchParams, 'p.first_name ilike \'%' . $objPaginationParams->getFirstName() . '%\'');
        $strLastName  = $objPaginationParams->getLastName() == '' ? '' : array_push($arrSearchParams, 'p.last_name ilike \'%' . $objPaginationParams->getLastName() . '%\'');
        $strMobile    = $objPaginationParams->getMobile() == '' ? '' : array_push($arrSearchParams, 'p.mobile_number ilike \'%' . $objPaginationParams->getMobile() . '%\'');
        $strUserName  = $objPaginationParams->getUserName() == '' ? '' : array_push($arrSearchParams, 'u.username ilike \'%' . $objPaginationParams->getUserName() . '%\'');
        $strFromDate  = $objPaginationParams->getFromDate() == null ? '' : array_push($arrSearchParams, 'u.created_on::date >= \'' . $objPaginationParams->getFromDate() . '\'::date');
        $strToDate    = $objPaginationParams->getToDate() == null ? '' : array_push($arrSearchParams, 'u.created_on::date <= \'' . $objPaginationParams->getToDate() . '\'::date');

        $strSql = 'SELECT
                u.id,
                u.user_type_id,
                u.reference_id,
                u.username,
                p.first_name,
                p.last_name,
                p.mobile_number,
                ut.name as user_type
            FROM users u
            LEFT JOIN user_types ut ON u.user_type_id = ut.id
            JOIN employees e ON (u.reference_id = e.id)
            JOIN persons p on p.id = e.person_id
            WHERE ' . implode(' AND ', $arrSearchParams) . '
            ORDER BY u.id
            OFFSET ' . $objPaginationParams->getOffset() . ' LIMIT ' . $objPaginationParams->getLimit() . '';

        $arrstdUsers = DB::select($strSql);

        foreach ($arrstdUsers as $stdUser) {
            $arrstrUser            = json_decode(json_encode($stdUser), true);
            $this->m_arrobjUsers[] = new User($arrstrUser);
        }

        return array_values($this->m_arrobjUsers);
    }

    public function fetchUserByEmailAddress(string $strEmailAddress): array
    {
        $strSql = 'SELECT
                    u.*
                FROM users u
                JOIN employees e ON (e.id = u.reference_id)
                WHERE u.username= \'' . $strEmailAddress . '\'';

        $arrstdUsers = DB::select($strSql);

        if (!valArr($arrstdUsers)) {
            return [];
        }
        foreach ($arrstdUsers as $stdUser) {
            $arrstrUser    = json_decode(json_encode($stdUser), true);
            $arrObjUsers[] = new User($arrstrUser);
        }
        return array_values($arrObjUsers);
    }

    public function fetchUserByMobileNumber(string $strMobileNumber): array
    {
        $strSql = 'SELECT
                    u.*
                FROM users u
                JOIN employees e ON (e.id = u.reference_id)
                JOIN persons p ON (p.id = e.person_id)
                WHERE p.mobile_number= \'' . $strMobileNumber . '\'';

        $arrstdUsers = DB::select($strSql);

        if (!valArr($arrstdUsers)) {
            return [];
        }
        foreach ($arrstdUsers as $stdUser) {
            $arrstrUser    = json_decode(json_encode($stdUser), true);
            $arrObjUsers[] = new User($arrstrUser);
        }
        return array_values($arrObjUsers);
    }

}