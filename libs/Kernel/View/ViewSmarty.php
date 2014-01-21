<?php
namespace NanoFramework\Kernel\View;
use NanoFramework\Kernel;
use NanoFramework\Utilities;

include "Smarty/libs/Smarty.class.php";

/**
* ViewSmarty
*
* @package NanoFramework\Kernel\View
* @author Stéphane BRUN <stephane.brun@sbnet.fr>
* @version 0.0.4
*/
class ViewSmarty extends Kernel\Event\Observable implements iViewEngine
{    
    public $flash;

    protected $layout;
    protected $vars = array();
    
    private $request;
    private $response;
    
    public function __construct($layout=null)
    {
        $this->layout = $this->set_layout($layout);        
        $this->request = Kernel\Request::get_instance();
        $this->response = Kernel\Response::get_instance();
        $this->flash = Utilities\Flash::get_instance();
    }
    
    public function __get($name)
    {  
        return $this->vars[$name];      
    }

    public function __set($name, $value)
    {        
        $this->vars[$name] = $value;
    }

    public function __isset($name)
    {        
        return isset($this->vars[$name]);
    }

    public function __unset($name)
    {        
        unset($this->vars[$name]);
    }

    /**
    * Set the layout
    */
    public function set_layout($layout)
    {
        $this->layout = $layout;
        return true;
    }

    /**
    * Render the view
    */
    public function _render($controller, $action, $no_layout=false)
    {    
        $smarty = new \Smarty();
        $smarty->cache_dir = DIR_TEMP.'/Smarty'; 
        $smarty->compile_dir = DIR_TEMP.'/Smarty'; 
        $smarty->caching = $GLOBALS['env']['environment']=='development'?false:true;
        $smarty->assign($this->vars);
        
        // This is needed if you want to use the helpers
        $smarty->allow_php_tag = true;
     
        $smarty->template_dir = array($GLOBALS['env']['DIR_VIEWS'].'layouts');
     
        // No layout for ajax calls and response of types different to html
        if($this->request->is_ajax() || ($this->response->get_current_type()!=='html'))
        {
            $this->layout = null;            
        }           
        
        // Need to render with the layout ?
        if(is_file($GLOBALS['env']['DIR_VIEWS'].'layouts/'.$this->layout.'.tpl'))
        {        
            $smarty->assign('content_for_layout', $GLOBALS['env']['DIR_VIEWS'].$controller.DIRECTORY_SEPARATOR.$action.".tpl");
            $out = $smarty->fetch($this->layout.'.tpl'); 
        }
        else
        {
            $out = $smarty->fetch($action.'.tpl');
        }        
        
        return $out;
    }
}
