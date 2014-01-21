<?php
namespace NanoFramework\Kernel;

/**
* Seringue, the dependency injector
*
* @package NanoFramework\Kernel
* @author Stéphane BRUN
* @version 0.0.1 
*/
class Seringue
{       
    private static $_instance;
    protected $data;

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
        $this->data = array();
    }

    /**
    * Magic getter
    *
    * @param string $name name of the data to get
    * @return mixed the data stored, null if no data found
    * @author Stéphane BRUN
    */
    public function __get($name)
    {
        if(isset($this->data[$name]))
        {
            if(is_callable($this->data[$name]))
            {
                return $this->data[$name]();
            }
            else
            {
                return $this->data[$name];                
            }
        }

        return null;
    }

    /**
    * Magic setter
    *
    * @author Stéphane BRUN
    * @param string $name name of the data to store
    * @param string $value the data itself
    */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
    * Check if the data exists 
    *
    * @param string $name name of the data to check
    * @return bool
    * @author Stéphane BRUN
    */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
    *  Remove a data
    *
    * @param string $name name of the data to remove
    * @author Stéphane BRUN
    */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }
}
