<?php
declare (strict_types = 1);

namespace App\Application\Actions\User;

use App\Domain\User\User;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Library\Encryption;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as V;
use stdClass;

class ChangeUserPasswordAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $arrstrRequestParameters = get_object_vars($this->getFormData());

        $objValidation = $this->m_objValidator->validateRequestArrays(['user' => (array) $arrstrRequestParameters['passwordDetails']], [
            'current_password' => V::noWhitespace()->userValidation(['id' => $arrstrRequestParameters['id']]),
        ], 2);

        if ($objValidation->failed()) {
            $arrstrErrors = $objValidation->getErrors();
            return $this->respondWithErrorMessages($arrstrErrors);
        }

        $objInMemoryUserRepository = new InMemoryUserRepository();
        $objUser                   = $objInMemoryUserRepository->findUserOfId($arrstrRequestParameters['id']);

        $objEncryption        = new Encryption;
        $strDecryptedPassword = $objEncryption->decryptText($objUser->getAttribute('password_encrypted'));

        if ($arrstrRequestParameters['passwordDetails']->current_password === $strDecryptedPassword) {
            $objUser = new User();

            $objUser->setAttribute('id', $arrstrRequestParameters['id']);
            $objUser->setAttribute('password_encrypted', $objEncryption->encryptText($arrstrRequestParameters['passwordDetails']->new_password));
            $objUser->setAttribute('is_password_changed', true);
            $objUser->exists = true;

            DB::beginTransaction();
            try {
                $objUser->update();
            } catch (\Illuminate\Database\QueryException $objException) {
                DB::rollback();
                throw new Exception('Failed to update person information.' . $objException->getMessage());
            }

        }

        DB::commit();

        $stdNurseryManagerOrder          = new stdClass();
        $stdNurseryManagerOrder->message = 'NurseryManager information updated successfully.';

        return $this->respondWithData($stdNurseryManagerOrder);
    }
}