<?php
namespace NanoFramework\Kernel;
use NanoFramework\Utilities;

/**
* The route class
*
* @package NanoFramework\Kernel
* @author BRUN Stéphane <stephane@sbnet.fr>
* @version 0.0.1 - 08/01/2010 - BRUN Stéphane
* @version 1.0.0 - 08/03/2013 - BRUN Stéphane
*/
class Route
{
    private static $_instance;

    protected $routes;
    protected $route_config;  
   
    public static function get_instance()
    {
        if(is_null(static::$_instance))
        {
            $c = __CLASS__;
            static::$_instance = new $c;
        }
        return static::$_instance;
    }
   
    public function __construct()
    {
    }
       
    /**
    * Get the route configuration object
    */
    public function get_route_config()
    {   
        if(!$this->route_config)
        {
            include(DIR_CONFIG.'route.php');
            $this->set_route_config($_ROUTE);
        }
    
        return $this->route_config;
    }

    /**
    * Set the route configuration object
    */
    public function set_route_config($route)
    {   
        $this->route_config = $route;
    }

    /**
    * Route decoding
    *
    * This one decodes the route accordingly to the configuration/route.php file
    * module, controller, action and additionnal parameters are returned as an array if the route decodes successfully
    *
    * @returns array module, controller, action and parameters
    */
    public function route_decode($url = null)
    {
        if($url == null)
        {
            $url = $_SERVER["REQUEST_URI"];
        }

        $current_route = $variables_array = $parameters = array();
        $route_found = false;

        // Get the route config into $this->route_config
        $this->get_route_config();
        $module_config = $this->route_config[$GLOBALS['env']['MODULE_NAME']];        

	    // Strip all the parameters after the ? (they are in the $_GET array)
        $query = preg_replace("/\?(.*)$/", '', $url);
        unset($_GET[$query]);

        // Strip the first parameter if it's /xxx.php
        $query = preg_replace("/^\/(.*)\.php/", '', $query);

        // Look on each routes to find a match
        foreach($module_config["routes"] as $route_name=>$route)
        {
            // Extract the parameter from the toute
            $route_parameters = $this->get_parameters($route["route"]);

            // Create a regexp to check if the route matches the query
            $match_reg = "{$route['route']}";
            $match_reg = str_replace('/', '\/', $match_reg);
            $match_reg = "/{$match_reg}$/";
            
            // Match a parameter
            foreach($route_parameters as $route_parameter)
            {
                $match_reg = str_replace(":{$route_parameter}", "([a-zA-Z0-9_\-=%\.]+)", $match_reg);
            }
            
            // Check if the route matches the url
            if(preg_match($match_reg, $query, $query_parameters))
            {
                $route_found = true;
                $current_route = $route;
                break;
            }
        }
        $parameters['module'] = $GLOBALS['env']['MODULE_NAME'];

        // Route not found
        if($route_found === false)
        {
            Seringue::get_instance()->log_nano->addInfo(_('Route schema not found routing to default controller/action'));
		    $parameters['controller'] = $module_config['default_controller'];
		    $parameters['action'] = $module_config['default_action'];
        }
        else
        {
            // Extract the parameters from the query
            foreach($route_parameters as $i=>$route_parameter)
            {
                // The first element of $query_parameters is the query itself, we don't need it
                $variables_array[$route_parameter] = $query_parameters[$i+1];
            }

            // Get the controller/action from the variables, route or default module config
            if(isset($variables_array["controller"]))
            {
		        $parameters['controller'] = $variables_array["controller"];
            }
            else if(isset($current_route["controller"]))  
            {
		        $parameters['controller'] = $current_route["controller"];
		    }
		    else
		    {
		        $parameters['controller'] = $module_config["default_controller"];		    
		    }
		    
            if(isset($variables_array["action"]))
            {
		        $parameters['action'] = $variables_array["action"];
            }
            else if(isset($current_route["action"]))  
            {
		        $parameters['action'] = $current_route["action"];
		    }
		    else
		    {
		        $parameters['action'] = $module_config["default_action"];		    
		    }

            Seringue::get_instance()->log_nano->addInfo(_("Route found : {$route['route']} -> {$parameters['controller']}/{$parameters['action']}"));		              
        } 

        // Mix all parameters together
		$parameters = array_merge($parameters, $_FILES);   
		$parameters = array_merge($parameters, $_POST);   
		$parameters = array_merge($parameters, $_GET);  
		$parameters = array_merge($parameters, $variables_array);

        // Add the controller's namespace in front of the controller's class
        $parameters['controller'] = "\\Controllers\\".$parameters['controller'];               

		return $parameters;    
    }

    /**
    * Route encoding
    *
    * If the url paramter is defined as <route_name>?p=v&p2=v2... then the corresponding 
    * url matching to the route definition will be returned
    *
    * Otherwise, the url parameter will be returned as is.
    *
    * $module is an optionnal parameter, it defines the module to make the route for, if not defined the 
    * current module name will be used
    *
    * @param string $url
    * @param string $module 
    * @return string url
    */
    public function url($url, $module=null)
    {        
        $routes = $this->get_route_config();
        
        if(!$module)
        {
            $module = $GLOBALS['env']['MODULE_NAME'];
        }
        $r = null;
        
        $parts = parse_url($url);
        
        // The url is complete, it starts with http://
        if(isset($parts["scheme"]))
        {
            return $url;
        }
        
        $route_name = $parts["path"];                            
        if(array_key_exists($route_name, $routes[$module]["routes"]))
        {
            $r = $route = $routes[$module]["routes"][$route_name]["route"];

            $route_parameters = $this->get_parameters($route);            
            if(!is_array($route_parameters))
            {
                $route_parameters = array();
            }
        }
        else
        {
            return $url;
        }

        $parameters = array();
        
        if(isset($parts["query"]))
        {
            $p = explode("&", $parts["query"]);
            foreach($p as $parameter)
            {
                list($name, $value) = explode("=", $parameter);
                $parameters[$name] = $value;
            }
        }
        
        // Parameters that are included in the route path
        foreach($route_parameters as $route_parameter)
        {        
            if(array_key_exists($route_parameter, $parameters))
            {
                $r = str_replace(":$route_parameter", urlencode($parameters[$route_parameter]), $r);                        
                unset($parameters[$route_parameter]);
            }
        }
                  
        // Other parameters
        foreach($parameters as $name=>$parameter)
        {
            if(strpos($r, "?") === false)
            {
                $r .= "?$name=".urlencode($parameter);                    
            }
            else
            {
                $r .= "&$name=".urlencode($parameter);
            }            
        }
            
        return $r;        
    } 
    
    public function get_404($module=null)
    {
        $routes = $this->get_route_config();
        $r = null;

        if($module!==null && isset($routes[$module]['not_found']))
        {
            $r = $routes[$module]['not_found'];
        }
        else if(isset($GLOBALS['env']['MODULE_NAME']))
        {
            $r = $routes[$GLOBALS['env']['MODULE_NAME']]['not_found'];
        }
        return $r;
    }
    
    private function get_parameters($route)
    {
        preg_match_all('/:([a-zA-Z]*)/i', $route, $route_parameters);
 
        return $route_parameters[1];            
    }
}
