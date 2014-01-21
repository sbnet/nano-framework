<?php
namespace NanoFramework\Kernel\Event;

/**
* Observer Class
*
* @package NanoFramework\Kernel\Event
* @author Stephane BRUN
* @author Jean-Philippe SERAFIN
*/
final class Observer
{   
    protected static $_instance;    
    protected $_observed_events=array();

    public static function get_instance()
    {
        if(is_null(static::$_instance))
        {           
            $c=__CLASS__;   
            static::$_instance = new $c;
        }
        return static::$_instance;
    }
        
    public function observe($observed_class, $event, $called_class, $called_method, $arguments=array())
    {
        $this->_observed_events[] = array(
            'observed_class' => $observed_class, 
            'event' => $event, 
            'called_class' => $called_class, 
            'called_method' => $called_method,
            'arguments' => $arguments
        );
    }   
     
    public function call_back($observed_class, $event)
    {
        $return_value = true;     
        foreach($this->_observed_events as $key=>$value)
        {             
            // We are using namespaces, we need to add a slash in front of the observed class name
            $observed_class = "\\".$observed_class;
            
            if($value['observed_class'] == $observed_class && $value['event']==$event)
            {
                $called_class = $value['called_class'];
                $called_method = $value['called_method'];
                if(call_user_func_array(array($called_class, $called_method), $value['arguments']) === false)
                {
                    $return_value = false;
                }
            }
        }
        
        return $return_value;
    }     
}
