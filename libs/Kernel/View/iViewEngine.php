<?php 
namespace NanoFramework\Kernel\View;

/**
* Interface for the view engine
*
* @package NanoFramework\Kernel\View
* @author BRUN Stéphane <stephane@sbnet.fr>
*/
interface iViewEngine
{
    public function _render($controller, $action, $no_layout=false);
    public function set_layout($layout);
    public function set_view_name($view_name);

    public function __construct($layout, $seringue);
    public function __get($name);
    public function __set($name, $value);
    public function __isset($name);
    public function __unset($name);
}

