<?php
namespace NanoFramework\Kernel;
use NanoFramework\Utilities;
use NanoFramework\Utilities\Cache;

/**
* Dispatcher
*
* @package NanoFramework\Kernel
* @author Stéphane BRUN <stephane@sbnet.fr>
* @version 0.0.1
* @version 0.0.2 : New view engine
* @version 0.0.3 : Cache actions
* @version 0.0.4 : Seringue
* @version 0.0.5 : New logger system
*/
class Dispatcher extends Event\Observable
{
    protected static $_instance;

    protected $security;
    protected $log;
    protected $route;
    protected $request;
    protected $response;
    protected $controller;
    protected $action;
    protected $i18n;
    protected $view_engine_class;
    protected $cache;
    protected $cache_config=array();
    protected $container;

    public function __construct()
    {
        // Required because some times Doctrine and more generally spl_autoload_register()
        // doesn't work when used with an opcode cache (APC)
        register_shutdown_function("session_write_close");
        session_start();

        $this->container = Seringue::get_instance();
        $this->container->page = new Utilities\Page($GLOBALS['env']['environment']=="production"?false:true);

        $this->route = Route::get_instance();
        $this->request = Request::get_instance();
        $this->response = Response::get_instance();

        $this->log = $this->container->log_nano;

        $this->i18n = Utilities\I18N::get_instance();

        $this->set_view_engine(VIEW_ENGINE);


        $this->cache = new Cache\Cache(new Cache\File(DIR_CACHE));
        $this->get_cache_config();

        $this->security = Security::get_instance();
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
    * Set the view engine
    *
    * @param string $engine
    */
    public function set_view_engine($engine)
    {
        $this->view_engine_class = '\NanoFramework\Kernel\View\View'.$engine;
    }

    /**
    * Set the current controller
    *
    * @param string $controller
    */
    public function set_controller($controller)
    {
        $this->controller = $controller;
    }

    /**
    * Get the current controller
    *
    * @return string the current controller
    */
    public function get_controller()
    {
        return $this->controller;
    }

    /**
    * Set the current action
    *
    * @param string $action
    */
    public function set_action($action)
    {
        $this->action = $action;
    }

    /**
    * Get the current action
    *
    * @return string the current action
    */
    public function get_action()
    {
        return $this->action;
    }

    /**
    * Run and render a partial
    *
    * @param string $module module's name
    * @param string $controller controller's name
    * @param string $action action's name
    * @param array $parameters parameters, will be available inside the controller object
    * @return string the content for the rendered partial
    */
    public function _partial($module, $controller, $action, $parameters=array())
    {
        $current_module = $GLOBALS['env']['MODULE_NAME'];
        $current_dir_controller = $GLOBALS['env']['DIR_CONTROLLERS'];
        $current_dir_view = $GLOBALS['env']['DIR_VIEWS'];

        // Jump into the module reality
        $GLOBALS['env']['MODULE_NAME'] = $module;
        $GLOBALS['env']['DIR_VIEWS'] = DIR_MODULES.$GLOBALS['env']['MODULE_NAME'].'/views/';
        $GLOBALS['env']['DIR_CONTROLLERS'] = DIR_MODULES.$GLOBALS['env']['MODULE_NAME'].'/controllers/';
        $this->i18n->initialize_domain($module, DIR_MODULES.$GLOBALS['env']['MODULE_NAME'].'/locales/');
        $this->i18n->use_domain($module);

        // If the corresponding class and method exist
        if(class_exists($controller) && method_exists($controller, $action))
        {
            $content = '';

            if(class_exists($this->view_engine_class))
            {
                $view = new $this->view_engine_class($controller, $this->container);
            }
            else
            {
                throw new Exception(_("The view engine {$this->view_engine_class} doesn't exists !"), E_ERROR);
            }

            $c = new $controller($view, $this->container);

            // map the parameters
            if($parameters)
            {
                foreach($parameters as $key=>$value)
                {
                    $c->$key = $value;
                }
            }

            // Run the action
            if($c->execute($action) !== false)
            {
                $content = $c->view->render($controller, $action, true);
            }
        }
        else // Partial not found
        {
            throw new Exception
(_("Partial $module/$controller/$action not found"), E_ERROR);
        }

        // Switch back to the real life
        $GLOBALS['env']['MODULE_NAME'] = $current_module;
        $GLOBALS['env']['DIR_VIEWS'] = $current_dir_view;
        $GLOBALS['env']['DIR_CONTROLLERS'] = $current_dir_controller;
        $this->i18n->use_domain($current_module);
        return $content;
    }

    /**
    * The heart of the MVC
    */
    public function dispatch()
    {
        $route = $this->route->route_decode();
        $this->request->set_parameters($route);

        $params = $this->request->get_parameters();
        $module_name = $params['module'];
        $controller_name = $params['controller'];
        $this->set_action($params['action']);

        // Set the gettext system for this controller
//        $this->i18n->reset_lang();
        $lang = $this->request->get_parameter('lang');
        if(empty($lang) && isset($_SESSION['lang']))
        {
            $lang = $_SESSION['lang'];
        }

        if($lang)
        {
            $this->i18n->initialize($lang, $module_name, $GLOBALS['env']['locale']['path']);
        }
        else
        {
            $this->i18n->initialize($GLOBALS['env']['locale']['default_culture'], $module_name, $GLOBALS['env']['locale']['path']);
        }
        $got_it = false;
        $content = "";

        // Check the security access
        if($this->security->access($module_name, $controller_name, $this->action) !== true)
        {
            throw new Exception
(_("Not handled security error for $module_name/$controller_name/{$this->action}"), E_ERROR);
        }

        // If the caching is active and the action is not in the $not_cached_actions array.
        if(CACHING == true && $this->request->method == 'GET')
        {
            if(   array_key_exists($module_name, $this->cache_config["not_cached_actions"])
               && array_key_exists($controller_name, $this->cache_config["not_cached_actions"][$module_name])
               && in_array($this->action, $this->cache_config["not_cached_actions"][$module_name][$controller_name]) )
            {
                $content = $this->get_from_cache($params);
                if(!empty($content))
                {
                    $got_it = true;
                }
            }
        }

        if(!$got_it)
        {
            // If the corresponding class exist
            if(class_exists($controller_name))
            {
                if(class_exists($this->view_engine_class))
                {
                    $view = new $this->view_engine_class($controller_name, $this->container);
                }
                else
                {
                    throw new Exception
(_("The view engine {$this->view_engine_class} doesn't exists !"), E_ERROR);
                }

                $this->set_controller(new $controller_name($view, $this->container));

                // Render the view only if the action hasn't returned false
                $x = $this->controller->execute($this->action);
                if($x !== false)
                {
                    // Remove the \Controllers namespace to find the correct view file
                    // $controller_name = str_replace("\\Controllers\\", "", $controller_name);

                    $content = $this->controller->view->render($controller_name, $this->action);
                    if(!empty($content))
                    {
                        // The headers will be sent to the client
                    }
                    else // No view
                    {
                        throw new Exception
(_("No view found for $controller_name => {$this->action}"), E_ERROR);
                    }
                }
            }
            else
            {
                $content = $this->forward_404($params);

                if($content == false)
                {
                    if(is_file(DIR_PUBLIC.'404.html'))
                    {
                        $content = file_get_contents(DIR_PUBLIC.'404.html');
                    }
                    else
                    {
                        $content = '<h1>'._("I've searched, but ... not found").'</h1>';
                    }
                }
            }

            // If caching, store into cache
            if(CACHING == true && $this->request->method == 'GET')
            {
               $this->store_into_cache($this->controller, $params, $content);
            }
        }
        echo $content;
    }

    /**
    * Redirect to another module/controller/action or url
    * It send an HTTP redirect
    *
    * @param string $destination action to forward to formated like : <module_name>/<controller_name>/<action_name>, or directly the url
    * @param array $params variables that will passed to the destination page, the key is the name of the variable
    * @param int $mode 301 (permanent) or 302, 302 is the default choise
    */
    public function _redirect_to($destination, $params=array(), $mode=302)
    {
        // Not an url ?
        if(!preg_match('/^http[s]?:\/\/(.*)$/', $destination))
        {
            $destination = WEB_PATH."{$destination}";
        }

        if($params)
        {
            $destination .= '?';
            foreach($params as $name=>$value)
            {
                if(is_numeric($name))
                {
                    $destination .= urlencode($value).'&';
                }
                else
                {
                    $destination .= urlencode($name).'='.urlencode($value).'&';
                }
            }

            // Remove the last '&'
            $destination = substr($destination, 0, -1);
        }

   	    if($mode == 301)
   	    {
       	    $this->response->set_header("Status: 301 Moved Permanently");
   	    }

   	    $this->response->set_header("Location: ".$destination);

	    // Be sure to redirect and nothing else
	    exit;
    }

    /**
    * Forward to another controller/action
    * Of course the forwarded view will be rendered instead of the first one.
    *
    * @param string $destination action to forward to formated like : <controller_name>/<action_name>
    * @param array $params variables that will be available inside the new controller, the key is the name of the variable
    */
    public function _forward_to($destination, $params=array())
    {
        list($controller, $action) = explode('/', $destination);

    	if(empty($action))
    	{
    	    $rc = $this->request->get_route_config();
    	    $action = $rc[$GLOBALS['env']['MODULE_NAME']]['default_action'];
        }

        // Set a new controller
        if(class_exists($controller) && (method_exists($controller, $action) || method_exists($controller, '_'.$action)) )
        {
            $new_view = new $this->view_engine_class($controller, $this->container);
            $new_controller = new $controller($new_view, $this->container);

            // Attach the parameters to this controller
            if($params)
            {
                foreach($params as $name=>$value)
                {
                    $new_controller->$name = $value;
                }
            }

            // Tell the dispatcher that the controller has changed
            $this->set_controller($new_controller);

            // And also the action so he'll be able to render the right view
            $this->set_action($action);
        }
        else
        {
            throw new Exception
(_("The controller/action $controller/$action doesn't exist"), E_ERROR);
            return false;
        }

        $this->log->addInfo(_("forwarded to -> $controller/$action"));

        return $new_controller->execute($action);
    }

    /**
    * Forward to 404
    *
    * @param array $params variables that will be available inside the new controller, the key is the name of the variable
    */
    public function _forward_404($params=array())
    {
        $r = false;

        $destination = $this->route->get_404();
        if($destination != '')
        {
            $this->forward_to($destination, $params);
            list($controller, $action) = explode("/", $destination);
            $r = $this->get_controller()->view->render($controller, $action);
        }
        return $r;
    }

    private function get_from_cache($params)
    {
        $key = $tis->get_cache_key($params);
        return $this->cache->get($key);
    }

    private function store_into_cache($controller, $params, $content)
    {
        $r = false;

        $key = $this->get_cache_key($params);
        $r = $this->cache->store($key, $content);

        return $r;
    }

    private function get_cache_key($params)
    {
        $key = "";
        foreach($params as $param)
        {
            $key .= $param;
        }
        return $key;
    }

    /**
    * Get the cache configuration
    */
    private function get_cache_config()
    {
        if(!$this->cache_config)
        {
            include(DIR_CONFIG.'cache.php');
            $this->cache_config["not_cached_actions"] = $not_cached_actions;
        }

        return $this->cache_config;
    }
}
