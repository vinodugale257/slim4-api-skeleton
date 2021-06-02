<?php
declare (strict_types = 1);

namespace App\Application\Actions\User;

use App\Domain\User\User;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use stdClass;

class DisableUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $intUserId = (int) $this->resolveArg('id');
        $objUser   = User::find($intUserId);

        $objUser->setAttribute('deactivated_by', 1);
        $objUser->setAttribute('deactivated_on', 'NOW()');

        if (!valObj($objUser, 'App\Domain\User\User')) {
            throw new Exception('Failed to find user record.');
        }

        try {
            $objUser->update();
        } catch (\Illuminate\Database\QueryException $objException) {
            throw new Exception('Failed to delete user record.');
        }

        $stdUser          = new stdClass();
        $stdUser->message = 'User record deleted successfully.';

        return $this->respondWithData($stdUser);
    }
}