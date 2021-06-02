<?php

declare (strict_types = 1);

namespace App\Domain\User;

use App\Application\MiddlewareException\InvalidLoginCredentialsException;
use App\Application\MiddlewareException\InvalidPermissionException;
use App\Domain\BaseDomain;
use App\Domain\UserType\UserType;
use App\Infrastructure\Persistence\Patient\InMemoryPatientRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use Library\Encryption;

class User extends BaseDomain
{
    protected $table = 'public.users';

    protected $fillable = [
        'id',
        'user_type_id',
        'reference_id',
        'person_id',
        'route_id',
        'group_name',
        'username',
        'email_address',
        'adhaar_number',
        'route_name',
        'pan_number',
        'password',
        'password_encrypted',
        'is_password_changed',
        'first_name',
        'last_name',
        'mobile_number',
        'user_type',
        'account_status_id',
        'profile_image',
        'address',
        'state_id',
        'state_name',
        'district_id',
        'district_name',
        'taluka_id',
        'taluka_name',
        'pin_code',
        'routes',
        'deleted_by',
        'deleted_on',
        'created_by',
        'created_on',
    ];

    public function authenticate($stdUserInfo)
    {
        $boolIsValidAccessToken = false;

        switch ($stdUserInfo->user_type_id) {
            case UserType::ADMIN:
            case UserType::EMPLOYEE:
                $objRepository = new InMemoryUserRepository();
                $objUser       = $objRepository->fetchUserByUserTypeIdByReferenceId($stdUserInfo->user_type_id, $stdUserInfo->reference_id);

                if (valObj($objUser, 'App\Domain\User\User')) {
                    $boolIsValidAccessToken = true;
                }
                break;

            case UserType::PATIENT:
                $objRepository = new InMemoryPatientRepository();
                $objUser       = $objRepository->fetchPatientById($stdUserInfo->reference_id);

                if (valObj($objUser, 'App\Domain\Patient\Patient')) {
                    $boolIsValidAccessToken = true;
                }
                break;

            default:
                $boolIsValidAccessToken = false;
                break;
        }

        if ($boolIsValidAccessToken) {
            return true;
        } else {
            throw new UserInvalidCredentialsException();
        }
    }

    public function login()
    {
        if (!valStr($this->getAttribute('username')) || !valStr($this->getAttribute('password'))) {
            throw new InvalidLoginCredentialsException();
        }

        $objRepository = new InMemoryUserRepository();
        $objUser       = $objRepository->fetchUserByUsername($this->getAttribute('username'));

        if (!valObj($objUser, 'App\Domain\User\User')) {
            throw new InvalidLoginCredentialsException();
        }

        $objEncryption        = new Encryption;
        $strDecryptedPassword = $objEncryption->decryptText($objUser->getAttribute('password_encrypted'));

        if (valStr($strDecryptedPassword) && $strDecryptedPassword == $this->getAttribute('password')) {

            return $objUser;
        } else {
            throw new InvalidLoginCredentialsException();
        }
    }

    public function checkRoutePermission($stdUserInfo, $strRouteName)
    {

        $objRepository = new InMemoryUserRepository();
        $objUser       = $objRepository->fetchRoutepermissionByUserTypeIdByReferenceId($stdUserInfo['user_type_id'], $stdUserInfo['reference_id'], $strRouteName);

        if (!valObj($objUser, 'App\Domain\User\User')) {
            throw new InvalidPermissionException();
        }

        return $objUser;
    }

    public function jsonSerialize()
    {
        return [
            'id'                  => $this->getAttribute('id'),
            'user_type_id'        => $this->getAttribute('user_type_id'),
            'reference_id'        => $this->getAttribute('reference_id'),
            'person_id'           => $this->getAttribute('person_id'),
            'route_id'           => $this->getAttribute('route_id'),
            'group_name'           => $this->getAttribute('group_name'),
            'username'            => $this->getAttribute('username'),
            'is_password_changed' => (int) $this->getAttribute('is_password_changed'),
            'email_address'       => $this->getAttribute('email_address'),
            'userType'            => $this->getAttribute('user_type'),
            'first_name'          => $this->getAttribute('first_name'),
            'last_name'           => $this->getAttribute('last_name'),
            'mobile_number'       => $this->getAttribute('mobile_number'),
            'adhaar_number'       => $this->getAttribute('adhaar_number'),
            'pan_number'          => $this->getAttribute('pan_number'),
            'account_status_id'   => $this->getAttribute('account_status_id'),
            'profile_image'       => $this->getAttribute('profile_image'),
            'address'             => $this->getAttribute('address'),
            'state_id'            => $this->getAttribute('state_id'),
            'state_name'          => $this->getAttribute('state_name'),
            'district_id'         => $this->getAttribute('district_id'),
            'district_name'       => $this->getAttribute('district_name'),
            'taluka_id'           => $this->getAttribute('taluka_id'),
            'taluka_name'         => $this->getAttribute('taluka_name'),
            'pin_code'            => $this->getAttribute('pin_code'),
            'route_name'              => $this->getAttribute('route_name'),
            'routes'              => $this->getAttribute('routes'),
        ];
    }
}