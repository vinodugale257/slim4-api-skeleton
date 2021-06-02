<?php
declare (strict_types = 1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class UserInfoAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {

        $stdUserInfo = $this->request->getAttribute('current_user');
        $routes = [];
        $intUserTypeId = (int) $stdUserInfo->user_type_id;
        $intRefrenceId = (int) $stdUserInfo->reference_id;
        $objUser       = $this->userRepository->findUserInfoByUserTypeIdByRefrenceId($intUserTypeId, $intRefrenceId);
        $routes       = $this->userRepository->fetchAllRoutepermissionByUserTypeIdByReferenceId($intUserTypeId, $intRefrenceId);
        foreach($routes as $route)
        {   
            // $objRoute['route_id'] = $route['route_id'];
            // $objRoute['reference_id'] = $route['reference_id'];
            // $objRoute['group_name'] = $route['group_name'];
            // $objRoute['route_name'] = $route['route_name'];
            $arrRoutes[] = $route['route_name'];
        }
        $objUser->setAttribute('routes', $arrRoutes);
        return $this->respondWithData($objUser);
    }
}