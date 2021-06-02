<?php
declare (strict_types = 1);

namespace App\Application\Actions\User;

use App\Domain\Employee\Employee;
use App\Domain\Person\Person;
use App\Domain\User\User;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Library\Encryption;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as V;
use stdClass;

class AddUserAction extends UserAction
{
    protected $m_strPassword;
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $arrstrRequestParameters = get_object_vars($this->getFormData());

        $objValidation = $this->m_objValidator->validateRequestArrays(['user' => $arrstrRequestParameters], [
            'email_address' => V::optional(V::email()->noWhitespace()->emailAddressValidation()),
            'mobile_number' => V::optional(V::noWhitespace()->mobileNumberValidation()),
        ], 2);

        if ($objValidation->failed()) {
            $arrstrErrors = $objValidation->getErrors();
            return $this->respondWithErrorMessages($arrstrErrors);
        }

        DB::beginTransaction();

        $intPersonId                             = $this->insertPerson($arrstrRequestParameters);
        $arrstrRequestParameters['person_id']    = $intPersonId;
        $intEmployeeId                           = $this->insertEmployee($arrstrRequestParameters['person_id']);
        $arrstrRequestParameters['reference_id'] = $intEmployeeId;

        $this->insertUser($arrstrRequestParameters);

        if ($arrstrRequestParameters['isEmail']) {
            $this->sendMail($arrstrRequestParameters);
        }

        DB::commit();
        $stdUser          = new stdClass();
        $stdUser->message = 'User information added successfully.';

        return $this->respondWithData($stdUser);
    }
    public function insertPerson($objPersonParameters)
    {
        $arrPersonInfo = (array) $objPersonParameters;
        $objPerson     = new Person();
        $objPerson->fill($arrPersonInfo);

        try {
            $objPerson->insert();
        } catch (\Illuminate\Database\QueryException $objException) {
            DB::rollback();
            throw new Exception('Failed to add person information.');
        }
        return $objPerson->getAttribute('id');
    }
    public function insertEmployee($intPersonId)
    {
        $objEmployee = new Employee();
        $objEmployee->setAttribute('person_id', $intPersonId);

        try {
            $objEmployee->insert();
        } catch (\Illuminate\Database\QueryException $objException) {
            DB::rollback();
            throw new Exception('Failed to add employee information.');
        }
        return $objEmployee->getAttribute('id');
    }

    public function insertUser($arrstrUserInfo)
    {
        $objUser = new User();
        $objUser->setAttribute('username', $arrstrUserInfo['email_address']);
        $objUser->setAttribute('user_type_id', $arrstrUserInfo['user_type_id']);
        $objUser->setAttribute('reference_id', $arrstrUserInfo['reference_id']);

        $this->m_strPassword = $this->password_generate(7);

        $objEncryption = new Encryption();
        $objUser->setAttribute('password_encrypted', $objEncryption->encryptText($this->m_strPassword));

        try {
            $objUser->insert();
        } catch (\Illuminate\Database\QueryException $objException) {
            DB::rollback();
            throw new Exception('Failed to add user information.');
        }
    }

    public function password_generate($chars)
    {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($data), 0, $chars);
    }

    public function sendMail($arrstrRequestParameters)
    {
        $objstrMailSubject = 'Shrividya ayurveda admin login details.';
        $objTransport      = (new \Swift_SmtpTransport(getenv('SMTP_HOST'), getenv('SMTP_PORT'), getenv('SMTP_PROTOCOL')))
            ->setUsername(getenv('SMTP_USERNAME'))
            ->setPassword(getenv('SMTP_PASSWORD'));

        $objMailer         = (new \Swift_Mailer($objTransport));
        $objstrMessageBody = 'Hello <b>' . $arrstrRequestParameters['first_name'] . ' ' . $arrstrRequestParameters['last_name'] . '</b>, Following are the login details for Shrividya admin panel.';

        $objstrMessageBody = $objstrMessageBody . '<br /> User Name : <b>' . $arrstrRequestParameters['email_address'] . '</b>';

        $objstrMessageBody = $objstrMessageBody . '<br /> Password : <b>' . $this->m_strPassword . '</b>';

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