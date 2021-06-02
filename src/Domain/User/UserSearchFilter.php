<?php

namespace App\Domain\User;

use Library\Pagination;

class UserSearchFilter extends Pagination
{

    protected $m_strFirstName;
    protected $m_strLastName;
    protected $m_strUserName;
    protected $m_strMobile;
    protected $m_strFromDate;
    protected $m_strToDate;

    public function __construct($arrstrSearchParameters)
    {
        parent::__construct($arrstrSearchParameters);

        if (isset($arrstrSearchParameters['first_name'])) {
            $this->setFirstName($arrstrSearchParameters['first_name']);
        }

        if (isset($arrstrSearchParameters['last_name'])) {
            $this->setLastName($arrstrSearchParameters['last_name']);
        }

        if (isset($arrstrSearchParameters['username'])) {
            $this->setUserName($arrstrSearchParameters['username']);
        }

        if (isset($arrstrSearchParameters['mobile_number'])) {
            $this->setMobile($arrstrSearchParameters['mobile_number']);
        }
        if (isset($arrstrSearchParameters['from_date'])) {
            $this->setFromDate($arrstrSearchParameters['from_date']);
        }
        if (isset($arrstrSearchParameters['to_date'])) {
            $this->setToDate($arrstrSearchParameters['to_date']);
        }

    }

    public function setFirstName($strFirstName)
    {
        $this->m_strFirstName = $strFirstName;
    }

    public function setLastName($strLastName)
    {
        $this->m_strLastName = $strLastName;
    }

    public function setUserName($strUserName)
    {
        $this->m_strUserName = $strUserName;
    }

    public function setMobile($strMobile)
    {
        $this->m_strMobile = $strMobile;
    }

    public function setFromDate($strFromDate)
    {
        $this->m_strFromDate = $strFromDate;
    }
    public function setToDate($strToDate)
    {
        $this->m_strToDate = $strToDate;
    }

    public function getFirstName()
    {
        return $this->m_strFirstName;
    }

    public function getLastName()
    {
        return $this->m_strLastName;
    }

    public function getUserName()
    {
        return $this->m_strUserName;
    }

    public function getMobile()
    {
        return $this->m_strMobile;
    }

    public function getFromDate()
    {
        return $this->m_strFromDate;
    }
    public function getToDate()
    {
        return $this->m_strToDate;
    }

}
