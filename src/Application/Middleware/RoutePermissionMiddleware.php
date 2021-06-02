<?php

namespace App\Application\Middleware;

use App\Application\MiddlewareException\AccessTokenExpiredException;
use App\Application\MiddlewareException\InvalidPermissionException;
use App\Domain\User\User;
use App\Domain\User\UserInvalidCredentialsException;
use App\Infrastructure\Persistence\Action\InMemoryActionRepository;
use Firebase\JWT\JWT;

class RoutePermissionMiddleware extends AbstractMiddleware
{
    protected $m_stdUserInfo;

    protected function beforeRouteExecution()
    {
        $objInMemoryActionRepository = new InMemoryActionRepository();

        $arrPublicRoutes = [];

        $arrobjPublicRoutes = $objInMemoryActionRepository->fetchAllPublicRoutes();

        foreach ($arrobjPublicRoutes as $objPublicRoute) {
            array_push($arrPublicRoutes, $objPublicRoute->name);
        }

        if (in_array($this->m_strRouteName, $arrPublicRoutes)) {
            return true;
        }

        // I have added these route inside database
        // $arrstrRoutes = ['welcome', 'patient-visit-add', 'change-user-password'];

        // if (in_array($this->m_strRouteName, $arrstrRoutes)) {
        //     return true;
        // }

        $stdUserInfo = (array) $this->m_objRequest->getAttribute('current_user');

        if (1 == $stdUserInfo['user_type_id']) {
            return true;
        }

        $objUser = new User();

        $objUserInfo = $objUser->checkRoutePermission($stdUserInfo, $this->m_strRouteName);

        if (!valObj($objUserInfo, 'App\Domain\User\User')) {
            throw new InvalidPermissionException();
        }
        return true;

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