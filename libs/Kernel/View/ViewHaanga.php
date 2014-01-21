<?php
namespace NanoFramework\Kernel\View;
use NanoFramework\Kernel;
use NanoFramework\Utilities;

include "Haanga/lib/Haanga.php";

/**
* ViewHaanga
*
* @package NanoFramework\Kernel\View
* @author Stéphane BRUN <stephane.brun@sbnet.fr>
* @version 0.0.1 
*/
class ViewHaanga extends Kernel\Event\Observable implements iViewEngine
{    
    public $flash;

    protected $layout;
    protected $vars = array();
    
    private $request;
    private $response;
    
    public function __construct($layout=null)
    {
        $this->set_layout($layout);        
        $this->request = Kernel\Request::get_instance();
        $this->response = Kernel\Response::get_instance();
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
        $config = array(
             'template_dir' => $GLOBALS['env']['DIR_VIEWS'],
             'cache_dir'    => DIR_TEMP.'/Haanga',
             'autoload'     => true,
             'compiler'     => array(),
        );
        \Haanga::configure($config);

        // No layout for ajax calls and response of types different to html
        if($no_layout || $this->request->is_ajax() || ($this->response->get_current_type()!=='html'))
        {
            $this->layout = null;            
        }     

        $layout_file = 'layouts/'.$this->layout.'.html';
        $this->content_for_layout = $controller.DIRECTORY_SEPARATOR.$action.".html";

        if($this->layout)
        {                    
            $out = \Haanga::Load($layout_file, $this->vars, true);      
        }
        else
        {
            $out = \Haanga::Load($this->content_for_layout, $this->vars, true);      
        }
                
        return $out;
    }
}
