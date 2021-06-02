<?php
declare (strict_types = 1);

namespace App\Application\Actions\User;

use App\Domain\Person\Person;
use App\Domain\User\User;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Library\Encryption;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as V;
use stdClass;

class UpdateUserAction extends UserAction
{
    protected $m_strPassword;
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $arrstrRequestParameters = get_object_vars($this->getFormData());

        // display($arrstrRequestParameters);exit;

        $objValidation = $this->m_objValidator->validateRequestArrays(['user' => $arrstrRequestParameters], [
            'email_address' => V::optional(V::email()->noWhitespace()->userValidation(['id' => $arrstrRequestParameters['id']])),
            'mobile_number' => V::optional(V::noWhitespace()->userValidation(['id' => $arrstrRequestParameters['id']])),
        ], 2);

        if ($objValidation->failed()) {
            $arrstrErrors = $objValidation->getErrors();
            return $this->respondWithErrorMessages($arrstrErrors);
        }

        DB::beginTransaction();

        $intPersonId                          = $this->insertPerson($arrstrRequestParameters);
        $arrstrRequestParameters['person_id'] = $intPersonId;

        $this->insertUser($arrstrRequestParameters);

        if ($arrstrRequestParameters['isEmail']) {

            $this->sendMail($arrstrRequestParameters);
        }

        DB::commit();
        $stdUser          = new stdClass();
        $stdUser->message = 'User information added successfully.';

        return $this->respondWithData($stdUser);
    }
    public function insertPerson($arrPersonParameters)
    {
        $arrPersonInfo       = $arrPersonParameters;
        $arrPersonInfo['id'] = $arrPersonParameters['person_id'];
        $objPerson           = new Person();
        $objPerson->fill($arrPersonInfo);
        $objPerson->exists = true;

        try {
            $objPerson->update();
        } catch (\Illuminate\Database\QueryException $objException) {
            DB::rollback();
            throw new Exception('Failed to update person information.');
        }
        return $objPerson->getAttribute('id');
    }

    public function insertUser($arrstrUserInfo)
    {
        $arrUserInfo['id']           = $arrstrUserInfo['id'];
        $arrUserInfo['username']     = $arrstrUserInfo['email_address'];
        $arrUserInfo['user_type_id'] = $arrstrUserInfo['user_type_id'];

        $objUser = new User();
        $objUser->fill($arrUserInfo);
        $objUser->exists = true;

        try {
            $objUser->update();
        } catch (\Illuminate\Database\QueryException $objException) {
            DB::rollback();
            throw new Exception('Failed to update user information.');
        }
    }

    public function sendMail($arrstrRequestParameters)
    {
        $objInMemoryUserRepository = new InMemoryUserRepository();
        $objUser                   = $objInMemoryUserRepository->findUserOfId($arrstrRequestParameters['id']);

        $objEncryption        = new Encryption;
        $strDecryptedPassword = $objEncryption->decryptText($objUser->getAttribute('password_encrypted'));

        $objstrMailSubject = 'Shrividya ayurveda admin login details.';
        $objTransport      = (new \Swift_SmtpTransport(getenv('SMTP_HOST'), getenv('SMTP_PORT'), getenv('SMTP_PROTOCOL')))
            ->setUsername(getenv('SMTP_USERNAME'))
            ->setPassword(getenv('SMTP_PASSWORD'));

        $objMailer         = (new \Swift_Mailer($objTransport));
        $objstrMessageBody = 'Hello <b>' . $arrstrRequestParameters['first_name'] . ' ' . $arrstrRequestParameters['last_name'] . '</b>, Following are the login details for Shrividya admin panel.';

        $objstrMessageBody = $objstrMessageBody . '<br /> User Name : <b>' . $arrstrRequestParameters['email_address'] . '</b>';

        $objstrMessageBody = $objstrMessageBody . '<br /> Password : <b>' . $strDecryptedPassword . '</b>';

        $objstrMessage = (new \Swift_Message($objstrMailSubject))
            ->setFrom([getenv('SMTP_FROM_ADDRESS') => getenv('SMTP_FROM_NAME')])
            ->setTo($arrstrRequestParameters['email_address'])
            ->setBody($objstrMessageBody, 'text/html');

        if ($objMailer->send($objstrMessage)) {
            echo "SUCCESS : Mail sent successfully";
        } else {
            DB::rollback();
            echo "ERROR : Failed to send email.";
        }
    }
}