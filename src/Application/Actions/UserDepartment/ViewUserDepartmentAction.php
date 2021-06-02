<?php
declare (strict_types = 1);

namespace App\Application\Actions\UserDepartment;

use Psr\Http\Message\ResponseInterface as Response;

class ViewUserDepartmentAction extends UserDepartmentAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {

        $intUserId = (int) $this->resolveArg('id');

        $arrobjUserDepartment = $this->userDepartmentRepository->findUserDepartmentsOfId($intUserId);

        return $this->respondWithData($arrobjUserDepartment);
    }
}
