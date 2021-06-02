<?php

namespace Library;

class Pagination
{
    protected $m_intCurrentPage;
    protected $m_intTotalCount;
    protected $m_intLimit  = 'ALL';
    protected $m_intOffset = 0;

    public function __construct($arrstrSearchParameters)
    {

        if (isset($arrstrSearchParameters['page'])) {
            $this->setCurrentPage($arrstrSearchParameters['page']);
        }

        if (isset($arrstrSearchParameters['itemsPerPage'])) {
            $this->setLimit($arrstrSearchParameters['itemsPerPage']);
        }

        if (isset($arrstrSearchParameters['page']) && isset($arrstrSearchParameters['itemsPerPage'])) {
            $this->setOffset($arrstrSearchParameters['page'], $arrstrSearchParameters['itemsPerPage']);
        }

    }

    public function setCurrentPage($intCurrentPage)
    {
        $this->m_intCurrentPage = $intCurrentPage;
    }

    public function setLimit($intLimit)
    {
        $this->m_intLimit = $intLimit;
    }

    public function setOffset($intCurrentPage, $intLimit)
    {
        $this->m_intOffset = isset($intCurrentPage) ? $intCurrentPage * $intLimit - $intLimit : 1;

    }

    public function getCurrentPage()
    {
        return $this->m_intCurrentPage;
    }

    public function getLimit()
    {
        return $this->m_intLimit;
    }

    public function getOffset()
    {
        return $this->m_intOffset;
    }

}