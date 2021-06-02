<?php

namespace App\Application\Validations\Rules;

use Respect\Validation\Rules\AbstractRule;

class BaseValidation extends AbstractRule 
{
    public $strFieldName;
    protected $arrstrRequestParameters;

    public function __construct( $arrstrRequestParameters = NULL )
    {
        $this->arrstrRequestParameters = $arrstrRequestParameters;
    }

    public function validate( $strInput )
    {
        $this->strFieldName = strtolower($this->name);

        $arrstrFieldName    = explode( '_', $this->name );
        $arrstrFieldName    = array_map('ucfirst', $arrstrFieldName);
        $strValFunctionName = 'val' . implode( '', $arrstrFieldName ); 
        
        return $this->$strValFunctionName( $strInput );
    }
}