<?php
namespace Helpers;

use NanoFramework\Utilities;
use NanoFramework\Kernel;

/**
 * Page helper class
 *
 * @author Stephane BRUN
 */
class Page
{
    static function render_for_title()
    {
        return Kernel\Seringue::get_instance()->page->render_for_title();
    }
    
    static function render_for_canonical()
    {
        return Kernel\Seringue::get_instance()->page->render_for_canonical();
    }

    static function render_for_metas()
    {
        return Kernel\Seringue::get_instance()->page->render_for_metas();
    }

    static function render_for_css()
    {
        return Kernel\Seringue::get_instance()->page->render_for_css();
    }

    static function render_for_javascripts()
    {    
        return Kernel\Seringue::get_instance()->page->render_for_javascripts();        
    }
   
    static function partial($controller, $action, $parameters=array())
    {
        return self::module_partial($GLOBALS['env']['MODULE_NAME'], $controller, $action, $parameters);
    }

    static function module_partial($module, $controller, $action, $parameters=array())
    {
        return \NanoFramework\Kernel\Dispatcher::get_instance()->partial($module, $controller, $action, $parameters);
    }   

    static function url($url, $module=null)
    {
        return \NanoFramework\Kernel\Route::get_instance()->url($url, $module);
    }   

    static function is($controller, $action=null)
    {
        $r = \NanoFramework\Kernel\Request::get_instance();

        $result = false;
        if($r->get_parameter("controller") == $controller)
        {
            if( ($action !== null && $action == $r->get_parameter("action"))  || ($action===null) ) 
            {
                $result = true;
            }
        }
        return $result;
    }       

}
