<?php

declare (strict_types = 1);

namespace App\Application\Actions\User;

use Exception;
use Library\Authentication;
use Psr\Http\Message\ResponseInterface as Response;

class LoginUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    // This function will handle 1. Astuter user login 2. Google authentication 3. Facebook authentication
    protected function action(): Response
    {
        $arrstrRequestParameters = get_object_vars($this->getFormData());

        try {
            $objAuthentication = new Authentication($arrstrRequestParameters);
            $stdUserInfo       = $objAuthentication->handleAuthentication();
        } catch (\PDOException $objException) {
            throw new Exception('Failed to hanlde authentication ' . $objException->getMessage());
        }

        $this->request = $this->request->withAttribute('current_user', $stdUserInfo);

        return $this->respondWithData([]);
    }
}
