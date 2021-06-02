<?php

namespace App\Application\Middleware;

use App\Application\MiddlewareException\AccessTokenExpiredException;
use App\Domain\User\User;
use App\Domain\User\UserInvalidCredentialsException;
use Firebase\JWT\JWT;

class UserAuthenticationMiddleware extends AbstractMiddleware
{
    protected $m_stdUserInfo;

    protected function beforeRouteExecution()
    {
        $arrstrRoutes = ['welcome', 'logout', 'user-login', 'notification-send-otp', 'states', 'districts', 'talukas', 'patient-visit-add', 'patient-visit-list'];

        if (in_array($this->m_strRouteName, $arrstrRoutes)) {
            return true;
        }

        $this->decodeAccessToken();
        $objUser = new User();
        return $objUser->authenticate($this->m_stdUserInfo);
    }

    protected function decodeAccessToken()
    {
        $arrstrServerParameters = $this->m_objRequest->getServerParams();
        $strAccessToken         = (isset($arrstrServerParameters['HTTP_AUTHORIZATION'])) ? $arrstrServerParameters['HTTP_AUTHORIZATION'] : '';

        if (!valStr($strAccessToken)) {
            throw new UserInvalidCredentialsException();
        }

        $this->m_stdUserInfo = JWT::decode(str_replace('Bearer ', '', $strAccessToken), getenv('JWT_SECRET'), ['HS256']);

        if (!isset($this->m_stdUserInfo->user_type_id) || !isset($this->m_stdUserInfo->reference_id) || !isset($this->m_stdUserInfo->expires)) {
            throw new UserInvalidCredentialsException();
        }

        if ($this->m_stdUserInfo->expires < time()) {
            throw new AccessTokenExpiredException();
        }

        $this->m_objRequest = $this->m_objRequest->withAttribute('current_user', $this->m_stdUserInfo);

        return true;
    }
}