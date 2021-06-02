<?php
declare (strict_types = 1);

namespace App\Application\Actions\UserType;

use Psr\Http\Message\ResponseInterface as Response;

class ListUserTypesAction extends UserTypeAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userTypes['userTypes'] = $this->userTypeRepository->findAll();

        return $this->respondWithData($userTypes);
    }

}