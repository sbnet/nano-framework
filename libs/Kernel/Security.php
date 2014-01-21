<?php
namespace NanoFramework\Kernel;

use NanoFramework\Utilities;

/**
* Security
* This class is a singleton
*
* @package NanoFramework\Kernel
* @author Stéphane BRUN <stephane@sbnet.fr>
* @version 1.0.0
*/
class Security
{       
    private static $_instance;
    private $security_config=null;
    public $deny_page;
    public $login_page;

    protected $log;    

    public function __construct()
    {
        $this->log = Seringue::get_instance()->log_nano;
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
    * Get the security configuration 
    * See the /configuration/security.php file
    * 
    * @return array The security configuration array
    */
    public function get_security_config()
    {   
        if($this->security_config === null)
        {
            include(DIR_CONFIG.'security.php');
            $this->security_config = $_SECURITY;

            $this->deny_page = $_SECURITY[$GLOBALS['env']['MODULE_NAME']]["deny_page"];
            $this->login_page = $_SECURITY[$GLOBALS['env']['MODULE_NAME']]["login_page"];            
        }
    
        return $this->security_config;
    }

    /**
    * Check if the access is granted to a module/action
    *
    * @param string $module module
    * @param string $controller controller
    * @param string $action  action
    * @param string $referer url to go back after login if needed
    * @param string $qs query_string
    * @return bool true if the acces is granted
    */
    public function access($module, $controller, $action, $referer = "", $qs = "")
    {
	    if(!$this->check_user($module, $controller, $action)) 
	    {
		    header("Location: ".$this->deny_page);
		    exit;
	    }
	    
	    $referer .= ($qs != "") ? "?".$qs : "";
        $_SESSION['referer'] = $referer;

        return true;
   }

    public function check_user($module, $controller, $action)
    {
        // Get the module/action credentials
        $config = $this->get_security_config();
        $need_verification = isset($config[$module][$controller]) && is_array($config[$module][$controller]) && array_key_exists($action, $config[$module][$controller]);

        if($need_verification)
        {
            // Get the user
            $user_object = isset($_SESSION["user_object"])?$_SESSION["user_object"]:null;

            if(!$user_object)           
            {
                return false;
            }
                        
            // Compare the user credentials to the action credentials
            $test_string = $config[$module][$controller][$action];
            foreach($_SESSION["credentials"] as $credential)
            {
                $test_string = str_replace($credential, "1", $test_string);                
            }
            $test_string = preg_replace("/([a-zA-Z]+)/i", "false", $test_string);
            str_replace("1", "true", $test_string); 
            
            unset($tmp_checker);
            $tmp_checker = create_function(null, "return ($test_string);");
            return $tmp_checker();
        }
        
        return true;
    }

    /**
    * Execute the login process
    *
    * @param mixed $db_user An object representative of the user table, must have a get_by_login_and_password($login, $password) method
    * @param string $login login
    * @param string $password password
    * @return Doctrine_Record if successfully logged in else false
    */
    public function login($db_user, $login, $password)
    {
        $result = false;
        $this->logout();
        
	    if(!empty($login) and !empty($password)) 
	    {
		    $user_object = $db_user->get_by_login_and_password($login, $password);

		    if($user_object != false) 
		    {
			    $result = $user_object;
		        $_SESSION["user_object"] = $user_object;
		        
			    if(isset($_SESSION['referer']) and !empty($_SESSION['referer'])) 
			    {
				    $next_page = $_SESSION['referer'];
				    unset($_SESSION['referer']);
				    header("Location: ".$next_page);
			    } 			    
		    }
	    } 
	    
	    return $result;
    }

    /**
    * Log out
    * Clear all the session and the credentials 
    */
    public function logout()
    {
        unset($_SESSION["logged"]);
        unset($_SESSION["user_object"]);
        $this->remove_all_credentials();
    }

    /**
    * Check if the user is logged
    * 
    * @return bool true is logged
    */    
    public function is_logged()
    {
        $r = false;
        if(isset($_SESSION["logged"]))
        {
            $r = $_SESSION["logged"];
        }
        
        return $r;
    }
    
    /**
    * Returns the current user object
    * 
    * @return mixed the current user object (null if none is set)
    */    
    public function get_user()
    {       
        $r = null;
        if(isset($_SESSION["user_object"]))
        {
            $r = $_SESSION["user_object"];
        }
        
        return $r;
    }
    
    /**
    * Add one credential
    *
    * @param string $credential
    */    
    public function add_credential($credential)
    {        
        $credential = trim($credential);
        
        if(!in_array($credential, $_SESSION["credentials"]))
        {
            $_SESSION["credentials"][] = $credential;
        }
    }

    /**
    * Add multiple credentials
    *
    * @param array $credentials
    */    
    public function add_credentials(array $credentials)
    {        
        foreach($credentials as $credential)
        {
            $this->add_credential($credential);
        }
    }

    /**
    * Remove only one credential
    *
    * @param string $credential
    */
    public function remove_credential($credential)
    {        
        if(in_array($credential, $_SESSION["credentials"]))
        {
            unset($_SESSION["credentials"][$credential]);
        }
    }

    /**
    * Remove all the credentials
    */
    public function remove_all_credentials()
    {   
        $_SESSION["credentials"] = array();     
    }    
}
