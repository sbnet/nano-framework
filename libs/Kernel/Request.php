<?php
namespace NanoFramework\Kernel;
use NanoFramework\Utilities;

/**
* The request class
*
* @package NanoFramework\Kernel
* @author BRUN Stéphane <stephane@sbnet.fr>
* @version 0.0.1 - 24/11/2007 - BRUN Stéphane
*/
class Request extends Event\Observable
{ 
    private static $_instance;
    private $_xhr = false;    
    private $_parameters = array();  
   
    public $method;
    
    public function __construct()
    {
        $this->method = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null;

    	if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == 'XMLHttpRequest')
    	{
    		$this->_xhr = true;
    	}
    	else
    	{
    		$this->_xhr = false;
        }
    }  
    
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
    * Check if called by XHR
    *
    * @return bool true if the request is an ajax call
    */
    public function is_ajax()
    {
        return $this->_xhr;
    }
    
    /**
    * Get a parameter
    *
    * All the parameters are available, the GET and the Post are merged (POST has precedence)
    *
    * @param string name of the parameter to get
    * @param mixed default value
    * @return mixed value of the parameter to get (null if nothing found)
    */
    public function _get_parameter($name, $default=null)
    {
        $r = null;
        
        if(isset($this->_parameters[$name]))        
        {
            $r = $this->_parameters[$name];
        }
        else
        {
            $r = $default;
        }
        
        return $r;
    }

    /**
    * Get the parameters array
    *
    * @return array 
    */
    public function _get_parameters()
    {
        return $this->_parameters;
    }
    
    /**
    * Set a parameter, can be usefull for modifying a value
    * 
    * @param string name of the parameter to store
    * @param mixed value of the parameter to store
    */
    public function _set_parameter($name, $value)
    {
        $this->_parameters[$name] = $value;
    }    

    /**
    * Set parameters, can be usefull for modifying some values at a time
    * 
    * @param array parameters to store
    */
    public function _set_parameters($parameters)
    {
        if(is_array($parameters))
        {
            foreach($parameters as $name=>$value)
            {
                $this->_parameters[$name] = $value;
            }
        }
    }  
    
    /**
    * Replace the current session by another one
    * 
    * WARNING : THE CURRENT SESSION WILL BE DESTROYED
    * 
    * @param string session_id of the session to reload
    */
    public function _switch_session($session_id) 
    {
        if($session_id)
        {
            session_unset();
            session_destroy();
            session_id($session_id);
            session_start();
        }        
    }      
}
