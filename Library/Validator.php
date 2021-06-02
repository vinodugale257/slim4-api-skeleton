<?php

namespace Library;

use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    protected $m_arrstrErrors;

    public function __construct()
    {
        $this->m_arrstrErrors = [];
    }

    public function validateRequest($objRequest, array $arrobjRules)
    {
        foreach ($arrobjRules as $strField => $objRule) {
            try {
                $objRule->setName(ucfirst($strField))->assert($objRequest->getParam($strField));
            } catch (NestedValidationException $objException) {
                $this->m_arrstrErrors[$strField] = $objException->getMessages();
            }
        }

        //$_SESSION['errors'] = $this->m_arrstrErrors;

        return $this;
    }

    public function validateRequestArrays($arrmixRequests, array $arrobjRules, $intArrayDepthLevel = 1)
    {
        switch ($intArrayDepthLevel) {
            case 2:
                foreach ($arrmixRequests as $intKeyLevel1 => $arrmixRequestLevel1) {
                    $arrstrErrors = $this->validateRequestArray($arrmixRequestLevel1, $arrobjRules);
                    if (valArr($arrstrErrors)) {
                        $this->m_arrstrErrors[$intKeyLevel1] = $arrstrErrors;
                    }
                }
                break;

            case 3:
                foreach ($arrmixRequests as $intKeyLevel2 => $arrmixRequestLevel2) {
                    foreach ($arrmixRequestLevel2 as $intKeyLevel3 => $arrmixRequestLevel3) {
                        $arrstrErrors = $this->validateRequestArray($arrmixRequestLevel3, $arrobjRules);
                        if (valArr($arrstrErrors)) {
                            $this->m_arrstrErrors[$intKeyLevel2][$intKeyLevel3] = $arrstrErrors;
                        }
                    }
                }
                break;
            default:
                $this->m_arrstrErrors = $this->validateRequestArray($arrmixRequests, $arrobjRules);
                break;
        }

        //$_SESSION['errors'] = $this->m_arrstrErrors;

        return $this;
    }

    public function validateRequestArray($arrstrRequest, array $arrobjRules)
    {
        $arrstrErrors = [];

        foreach ($arrobjRules as $strField => $objRule) {
            $strUcFirstField = ucfirst($strField);

            try {
                if (isset($arrstrRequest[$strField])) {
                    $objRule->setName($strUcFirstField)->assert($arrstrRequest[$strField]);
                } else {
                    $objRule->setName($strUcFirstField)->assert('');
                }
            } catch (NestedValidationException $objException) {
                $arrstrErrorMessages = $objException->getMessages();
                $strMessageField     = str_replace('_', ' ', $strUcFirstField);

                foreach ($arrstrErrorMessages as $strMessage) {
                    $arrstrErrors[$strField][] = str_replace($strUcFirstField, $strMessageField, $strMessage);
                }
            }
        }

        return $arrstrErrors;
    }

    public function failed()
    {
        return !empty($this->m_arrstrErrors);
    }

    public function getErrors()
    {
        return $this->m_arrstrErrors;
    }

    public function setErrors($arrstrErrors)
    {
        $this->m_arrstrErrors = $arrstrErrors;
    }
}
