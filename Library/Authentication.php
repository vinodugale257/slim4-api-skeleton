<?php

namespace Library;

use App\Domain\User\User;
use App\Domain\UserType\UserType;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

class Authentication
{
    protected $m_strUsername;
    protected $m_strPassword;
    protected $m_strOAuthClientType;
    protected $m_strAuthCode;
    protected $m_strAccessToken;
    protected $m_strMobileNumber;
    protected $m_strOTP;

    public function __construct($arrstrUserInfo)
    {
        if (isset($arrstrUserInfo['username'])) {
            $this->setUsername($arrstrUserInfo['username']);
        }

        if (isset($arrstrUserInfo['password'])) {
            $this->setPassword($arrstrUserInfo['password']);
        }

        if (isset($arrstrUserInfo['oauthClientType'])) {
            $this->setOauthClientType($arrstrUserInfo['oauthClientType']);
        }

        if (isset($arrstrUserInfo['mobileNumber'])) {
            $this->setMobileNumber($arrstrUserInfo['mobileNumber']);
        }

        if (isset($arrstrUserInfo['otp'])) {
            $this->setOtp($arrstrUserInfo['otp']);
        }
    }

    public function setUsername($strUsername)
    {
        $this->m_strUsername = $strUsername;
    }

    public function setPassword($strPassword)
    {
        $this->m_strPassword = $strPassword;
    }

    public function setOauthClientType($strOauthClientType)
    {
        $this->m_strOAuthClientType = $strOauthClientType;
    }

    public function setMobileNumber($strMobileNumber)
    {
        $this->m_strMobileNumber = $strMobileNumber;
    }

    public function setOtp($strOtp)
    {
        $this->m_strOTP = $strOtp;
    }

    public function getUsername()
    {
        return $this->m_strUsername;
    }

    public function getPassword()
    {
        return $this->m_strPassword;
    }

    public function getOauthClientType()
    {
        return $this->m_strOAuthClientType;
    }

    public function getMobileNumber()
    {
        return $this->m_strMobileNumber;
    }

    public function getOtp()
    {
        return $this->m_strOTP;
    }

    public function handleAuthentication()
    {
        if('mobile' == $this->getOauthClientType()) {
            return $this->handleMobileAuthentication();
        } else {
            return $this->handleBasicAuthentication();
        }
    }

    public function handleMobileAuthentication()
    {
        if (!valStr($this->getMobileNumber())) {
            throw new Exception('Mobile number is missing');
        }

        if (!valStr($this->getOtp())) {
            throw new Exception('OTP is missing');
        }

        $strSql = '';

        $arrstdResult = DB::select($strSql);

        if (!valArr($arrstdResult, 1, true)) {
            throw new Exception('Mobile number verification failed.');
        }

        $intUserTypeId   = $arrstdResult[0]->user_type_id;
        $intReferenceId  = $arrstdResult[0]->id;
        $objRepository   = new InMemoryNotificationRepository();
        $objNotification = $objRepository->fetchNotificationByUserTypeIdByReferenceIdByOtp($intUserTypeId, $intReferenceId, $this->getOtp());

        if (!valObj($objNotification, 'App\Domain\Notification\Notification')) {
            throw new Exception('Invalid OTP.');
        }

        $stdUserInfo = new stdClass();

        $stdUserInfo->user_type_id = $intUserTypeId;
        $stdUserInfo->reference_id = $intReferenceId;

        return $stdUserInfo;
    }

    public function handleBasicAuthentication()
    {
        if (!valStr($this->getUsername())) {
            throw new Exception('Username is missing');
        }

        $objUser = new User();
        $objUser->fill(['username' => $this->getUsername(), 'password' => $this->getPassword()]);
        $objUser = $objUser->login();

        $stdUserInfo = new stdClass();

        $stdUserInfo->user_type_id = $objUser->getAttribute('user_type_id');
        $stdUserInfo->reference_id = $objUser->getAttribute('reference_id');
        
        return $stdUserInfo;
    }
}