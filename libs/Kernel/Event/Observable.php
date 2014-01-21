<?php
namespace NanoFramework\Kernel\Event;

interface iObservable
{
    public function __call($method_name, $arguments);        
}

/**
* Observable Class
*
* @package NanoFramework\Kernel\Event
* @author Stephane BRUN
* @author Jean-Philippe SERAFIN
* @todo test the static observation
*/
abstract class Observable implements iObservable
{    
    public function __call($method_name, $arguments)
    {           
        $ret = false;
        if(Observer::get_instance()->call_back(get_class($this), 'before_'.$method_name))
        {        
            $ret = call_user_func_array(array($this, '_'.$method_name), $arguments);
        }        
        Observer::get_instance()->call_back(get_class($this), 'on_'.$method_name);           
        
        return $ret;
    }

/*
    public static function __callStatic($method_name, $arguments)
    {
        $ret = false;
        if(Observer::get_instance()->call_back(__CLASS__, 'before_'.$method_name))
        {
            $ret = call_user_func_array(array(__CLASS__, '_'.$method_name), $arguments);
        }
        Observer::get_instance()->call_back(__CLASS__, 'on_'.$method_name);       
        
        return $ret;
    }
*/
}
