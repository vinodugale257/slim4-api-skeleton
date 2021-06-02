<?php
namespace Library;

use App\Domain\ExternalApiLog\ExternalApiLog;
use App\Domain\NotificationType\NotificationType;
use App\Domain\Notification\Notification;
use App\Domain\UserType\UserType;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

class NotificationLibrary
{
    protected $m_strUserType;
    protected $m_strMobileNumber;

    public function __construct($arrstrUserInfo)
    {
        if (isset($arrstrUserInfo['userType'])) {
            $this->setUserType($arrstrUserInfo['userType']);
        }

        if (isset($arrstrUserInfo['mobileNumber'])) {
            $this->setMobileNumber($arrstrUserInfo['mobileNumber']);
        }
    }

    public function setUserType($strUserType)
    {
        $this->m_strUserType = $strUserType;
    }

    public function setMobileNumber($strMobileNumber)
    {
        $this->m_strMobileNumber = $strMobileNumber;
    }

    public function getUserType()
    {
        return $this->m_strUserType;
    }

    public function getMobileNumber()
    {
        return $this->m_strMobileNumber;
    }

    public function handleSenOTP()
    {
        if (!valStr($this->getMobileNumber())) {
            throw new Exception('Mobile number is missing');
        }

        $strSql = 'SELECT
                        *
                    FROM (
                        SELECT
                            e.id,
                            u.user_type_id,
                            p.mobile_number
                        FROM
                            users u
                            JOIN employees e ON( e.id = u.reference_id AND u.user_type_id = ' . UserType::ADMIN . ' )
                            JOIN persons p on(p.id = e.person_id)
                        WHERE
                            u.deactivated_on IS NULL
                            AND u.deleted_on IS NULL
                            AND p.mobile_number=\'' . $this->getMobileNumber() . '\'
                        UNION
                        SELECT
                            d.id,
                            ' . UserType::DISTRIBUTOR . ' as user_type_id,
                            p.mobile_number
                        FROM
                            distributors d
                            JOIN persons p ON(p.id = d.person_id)
                        WHERE
                            d.deleted_on IS NULL
                            AND p.mobile_number=\'' . $this->getMobileNumber() . '\'
                        UNION
                        SELECT
                            f.id,
                            ' . UserType::FARMER . ' as user_type_id,
                            p.mobile_number
                        FROM
                            farmers f
                            JOIN persons p on(p.id = f.person_id)
                        WHERE
                            f.deleted_on IS NULL
                            AND p.mobile_number=\'' . $this->getMobileNumber() . '\'
                        UNION
                        SELECT
                            a.id,
                            ' . UserType::AGRONOMIST . ' as user_type_id,
                            p.mobile_number
                        FROM
                            agronomists a
                            JOIN persons p on(p.id = a.person_id)
                        WHERE
                            a.deleted_on IS NULL
                            AND p.mobile_number=\'' . $this->getMobileNumber() . '\'
                        UNION
                        SELECT
                            nm.id,
                            ' . UserType::NURSERYMANAGER . ' as user_type_id,
                            p.mobile_number
                        FROM
                            nursery_managers nm
                            JOIN persons p on(p.id = nm.person_id)
                        WHERE
                            nm.deleted_on IS NULL
                            AND p.mobile_number=\'' . $this->getMobileNumber() . '\'
                    ) as sub
                    ORDER BY user_type_id
                    LIMIT 1';
        $arrstdResult = DB::select($strSql);

        if (!valArr($arrstdResult, 1, true)) {
            throw new Exception('Mobile number verification failed.');
        }

        $intUserTypeId  = $arrstdResult[0]->user_type_id;
        $intReferenceId = $arrstdResult[0]->id;

        $strOTP              = ('production' == getenv('APP_ENV')) ? rand(100000, 999999) : '123456';
        $strApiKey           = urlencode(getenv('SMS_API_KEY'));
        $arrstrMobileNumbers = [$this->getMobileNumber()];
        $strSender           = urlencode('ASTPLT');
        $strMessage          = rawurlencode('OTP to login Mahogani Vishwa Agro is ' . $strOTP . ' and valid till 2 minutes. Do not share it with anyone. - Astuter Technologies');
        $strMobileNumbers    = implode(',', $arrstrMobileNumbers);

        //Prepare data for POST request
        $arrstrPostData = array(
            'apikey'  => $strApiKey,
            'numbers' => $strMobileNumbers,
            'sender'  => $strSender,
            'message' => $strMessage,
        );

        if ('production' == getenv('APP_ENV')) {
            $objHandler = curl_init(getenv('SMS_API_URL'));

            curl_setopt($objHandler, CURLOPT_POST, true);
            curl_setopt($objHandler, CURLOPT_POSTFIELDS, $arrstrPostData);
            curl_setopt($objHandler, CURLOPT_RETURNTRANSFER, true);
            $strResponse = curl_exec($objHandler);
            curl_close($objHandler);
        } else {
            $strResponse = '{"status": "success"}';
        }

        $strErrorMessage = null;

        if (!valStr($strResponse)) {
            $boolIsSuccess   = 'false';
            $strErrorMessage = 'Invalid SMS api call response';
            //throw new Exception('Invalid SMS api call response');
        } else {
            $jsonResponse = json_decode($strResponse);

            if (is_null($jsonResponse)) {
                $boolIsSuccess   = 'false';
                $strErrorMessage = 'Failed to decode SMS api call response data';
                //throw new Exception('Failed to decode SMS api call response data');
            } elseif ('success' != $jsonResponse->status) {
                $boolIsSuccess   = 'false';
                $strErrorMessage = $jsonResponse->errors[0]->message;
            } else {
                $boolIsSuccess = 'true';
            }
        }

        $arrstrNotification = [
            'user_type_id'         => $intUserTypeId,
            'reference_id'         => $intReferenceId,
            'notification_type_id' => NotificationType::OTP,
            'message'              => $strOTP,
            'is_success'           => $boolIsSuccess,
            'error_message'        => $strErrorMessage,
        ];

        $objNotification = new Notification();
        $objNotification->fill($arrstrNotification);

        try {
            $objNotification->insert();
        } catch (\Illuminate\Database\QueryException $objException) {
            throw new Exception('Failed to add notification.');
        }
        $arrstrPostData['apikey'] = 'XXX';

        $arrstrExternalApiLog = [
            'service_name'  => 'notifications',
            'reference_id'  => $objNotification->getAttribute('id'),
            'request_data'  => json_encode($arrstrPostData),
            'response_data' => $strResponse,
            'error_message' => $strErrorMessage,
            'is_success'    => $boolIsSuccess,
        ];

        $objExternalApiLog = new ExternalApiLog();
        $objExternalApiLog->fill($arrstrExternalApiLog);

        try {
            $objExternalApiLog->insert();
        } catch (\Illuminate\Database\QueryException $objException) {
            throw new Exception('Failed to add external api log.');
        }

        if ('false' == $boolIsSuccess) {
            throw new Exception('Failed to send OTP.');
        }

        $stdNotification = new stdClass();

        $stdNotification->message = 'OTP sent successfully.';

        return $stdNotification;
    }
}