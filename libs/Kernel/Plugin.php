<?php
namespace NanoFramework\Kernel;

/**
* Plugin
*
* @package NanoFramework\Kernel
* @author Stéphane BRUN <stephane.brun@sbnet.fr>
* @version 0.0.1 
*/
class Plugin
{       
    /**
    * Enable a plugin
    *
    * It include the file with the same name as the plugin, located in the directory 
    * DIR_PLUGINS/<name of the plugin>
    *
    * It also run the static method \Plugins\<plugin>\<plugin>::init() if it exists
    *
    * @param mixed $plugin the name of the plugin to enable, can be an array of name to enable multiple plugins once
    */
    public static function enable($plugins)
    {       
        if(!is_array($plugins))
        {
            $plugins = array($plugins);
        }

        foreach($plugins as $plugin)
        {
            include DIR_PLUGINS.$plugin.'/'.$plugin.'.php';
            
            $class = "Plugins\\$plugin\\$plugin";
            if(class_exists($class) && method_exists($class, "init"))
            {
                $class::init();            
            }            
        }
    }       

    /**
    * Disable a plugin
    *
    * @todo all
    * @param mixed $plugin the name of the plugin to disable, can be an array of name to disable multiple plugins once
    */
    public static function disable($plugins)
    {
        if(!is_array($plugins))
        {
            $plugins = array($plugins);
        }

        foreach($plugins as $plugin)
        {
        }
    }        
}
