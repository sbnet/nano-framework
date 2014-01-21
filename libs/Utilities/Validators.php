<?php
/**
* Validators
*
* @package NanoFramework\Utilities
*/
namespace NanoFramework\Utilities;

/**
* Validators container, only static methods
* 
* @package NanoFramework\Utilities
* @class Validators
* @author Stéphane BRUN
* @version 0.0.1 
* @todo translations
*/
class Validators
{     

    const MESSAGE_IS_NOT_EMPTY = "can't be empty";
    const NOT_VALID = false;
    
    static public function is_not_empty($v, $params)
    {        
        if(empty($params['message']))
        {
            $params['message']="can't be empty";
        }
        
        if(empty($v))
        {
            return $params['message'];
        }
        return null;
    }

    static public function is_a_string($v, $params)
    {
        if(empty($params['message']))
        {
            $params['message']="must be a string";
        }

        if(!is_string($v))
        {
            return $params['message'];
        }
        return null;
    }

    static public function is_an_email($v, $params)
    {
        if(empty($params['message']))
        {
            $params['message']="must be an email";
        }

        if(!preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/', $v))
        {
            return $params['message'];
        }
        return null;
    }

    static public function is_an_url($v, $params)
    {
        if(empty($params['message']))
        {
            $params['message']="must be an url";
        }

        if(!preg_match('/^http[s]?:\/\/(.*)$/', $v))
        {
            return $params['message'];
        }
        return null;
    }

    static public function is_a_number($v, $params)
    {
        if(empty($params['message']))
        {
            $params['message']="must be a number";
        }

        if(!is_numeric($v))
        {
            return $params['message'];
        }
        return null;
    }

    static public function is_something($v, $params)
    {
        return null;
    } 
     
    static function valid($v, $params)
    {
        if(!preg_match($params['regex'], $v))
        {
            return $params['message'];
        }
        return null;
    }  
}
