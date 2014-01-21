<?php
/**
* Flash system
*
* @package NanoFramework\Utilities
*/
namespace NanoFramework\Utilities;
use NanoFramework\Kernel;

/**
* Flash system, used to store variables temporarily into the session, allowing
* to get them between two actions
*
* When a variable is readed from the flash, it is erased. 
*
* This class is a singleton
*
* @package NanoFramework\Utilities
* @author BRUN Stéphane <stephane@sbnet.fr>
* @version 1.0.0
*/
class Flash
{
    protected static $_instance;

    public function __construct()
    {
        if(!array_key_exists("flash", $_SESSION))
        {
            $_SESSION["flash"] = array();
        }
    }
    
    public function __clone(){}

    public static function get_instance()
    {
        if(is_null(static::$_instance))
        {               
            $c = __CLASS__;
            static::$_instance = new $c;
        }
        return static::$_instance;
    }

    /**
    * Get a variable from the flash and erase it
    *
    * @param string variable name
    * @return mixed variable value or null if not found
    */
    public function get($name)
    {        
        $r = null;
        
        if($this->has($name))
        {
            $r = $_SESSION["flash"][$name];
            unset($_SESSION["flash"][$name]);
        }
        
        return $r;
    }
    
    /**
    * Store a variable into the flash
    *
    * @param string variable name
    * @param mixed variable value
    */
    public function set($name, $value)
    {           
        $_SESSION["flash"][$name] = $value;
    }
    
    /**
    * Check if a variable is into the flash
    *
    * @param string variable name
    * @return bool 
    */
    public function has($name)
    {   
        return array_key_exists($name, $_SESSION["flash"]);    
    }
}
