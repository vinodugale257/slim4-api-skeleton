<?php

use Respect\Validation\Validator as V;

function display($strMixVar)
{
    foreach (func_get_args() as $strMixVar) {
        echo '<pre style="background-color:white; color:rgb(32, 56, 18);padding:5px; border: 1px solid black; border-radius: 4px;">', htmlentities(print_r($strMixVar, true)), '</pre>';
    }
}

function valStr($strString, $intLen = 1)
{
    $strString = ( false == valArr($strString) ) ? trim(( string ) $strString) : null;
    return ( true == isset($strString[0]) && $intLen <= strlen($strString) ) ? true : false;
}

function valObj($objInstance, $strClassName)
{
    return ($objInstance instanceof $strClassName) ? (bool) 1: (bool) 0;
}

function valArr($arrmixValues, $intCount = 1, $boolCheckForEquality = false)
{
    $boolIsValid = (is_array($arrmixValues) && $intCount <= count($arrmixValues) ) ? true : false;
    if ($boolCheckForEquality && $boolIsValid) {
        $boolIsValid = ( $intCount == count($arrmixValues) ) ? (bool) true : (bool) false;
    }
    return $boolIsValid;
}
