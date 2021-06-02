<?php
declare (strict_types = 1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {

        $intUserId = (int) $this->resolveArg('id');

        $objUser = $this->userRepository->findUserOfId($intUserId);

        return $this->respondWithData($objUser);
    }
}