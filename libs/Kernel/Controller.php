<?php
namespace NanoFramework\Kernel;
use NanoFramework\Utilities;

/**
* Controller
*
* This class is {@linkplain NanoFramework\Kernel\Event.Observable observable}
*
* @package NanoFramework\Kernel
* @author Stéphane BRUN <stephane.brun@sbnet.fr>
* @version 0.0.2 
*/
class Controller extends Event\Observable
{
    public $dispatcher;
    public $view;        
    public $request;
    public $response;
    public $route;
    public $flash;
    public $security;

    protected $seringue;
    public $page;
    public $log;
        
    public function __construct($view, $seringue)
    {
        $this->view = $view;

        $this->seringue = $seringue;
        $this->page = $this->seringue->page;
        $this->log = $this->seringue->log_nano;

        $this->dispatcher = Dispatcher::get_instance();
        $this->request = Request::get_instance();
        $this->response = Response::get_instance();
        $this->route = Route::get_instance();
        $this->flash = Utilities\Flash::get_instance();
        $this->security = Security::get_instance();

        $this->_init();
    }
    
    /**
    * Just an empty function called at the contruction
    * The trick is that it can be observed
    */
    public function _init()
    {
    }        

    /**
    * Set a layout for the view
    *
    * @param string $layout layout to use
    * @return bool true in case of success
    */
    public function render_with_layout($layout)
    {
        return $this->view->set_layout($layout);
    }
    
    /**
    * Set a layout for the view
    *
    * @deprecated Use render_with_layout() instead
    * @see NanoFramework\Kernel.Controller#render_with_layout
    * @param string $layout layout to use
    * @return bool true in case of success
    */
    public function render_with($layout)
    {
        throw new NanoFramework\Kernel\NanoException(_("Controller::render_with() is obsolete, use Controller::render_with_layout() instead"), E_WARNING);
        return $this->view->set_layout($layout);
    }

    /**
    * Set a specific view
    *
    * @param string $view_name name view to use
    * @return bool true in case of success
    */
    public function render_with_view($view_name)
    {
        return $this->view->set_view_name($view_name);
    }

    /**
    * Try to run an action
    *
    * @param string $action action's name
    * @return bool the result of the action, should be true or false, if false then no view will be rendered
    */
    public function _execute($action)
    {        
		if(method_exists($this, $action) || method_exists($this, '_'.$action))
		{
            return $this->$action();                
        }

        throw new NanoException(_("The action $action doesn't exist"), E_ERROR);
    }   
}
