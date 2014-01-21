<?php
/**
* The cache factory
*
* @package NanoFramework\Utilities\Cache
*/
namespace NanoFramework\Utilities\Cache;
use NanoFramework\Kernel;

/**
* Cache class
*
* @package NanoFramework\Utilities
* @author Stephane BRUN
*/
class Cache
{         
    private $driver;
    
    public function __construct($driver)
    {
        $this->set_driver($driver);
    }
    
    public function __get($name)
    {
        return $this->get($name);
    }
    
    public function __set($name, $value)
    {
        return $this->store($name, $value);
    }
    
    public function __isset($name)
    {
        return $this->check_for($name);
    }

    public function __unset($name)
    {
        return $this->delete($name);
    }

    public function set_driver($driver)
    {                                
        $this->driver = $driver;
        return $this->driver;
    }  

    public function get_driver()
    {                                
        return $this->driver;
    }  
    
    public function check_for($name)
    {
        return $this->driver->check_for($name);
    }
    
    public function get($name)
    {
        return $this->driver->get($name);
    }

    public function store($name, $data)
    {
        return $this->driver->store($name, $data);
    }

    public function delete($name)
    {
        return $this->driver->delete($name);
    }
        
    public function clear_all()
    {
        return $this->driver->clear_all();
    }    
}
