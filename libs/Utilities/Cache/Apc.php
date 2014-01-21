<?php
/**
* The APC cache
*
* @package NanoFramework\Utilities\Cache
*/
namespace NanoFramework\Utilities\Cache;

/**
* APC Cache class
*
* @author Stephane BRUN
*/
class Apc implements iCacheDriver
{    
    public $ttl = 3600; 
    
    public function __construct()
    {
        if(!function_exists("apc_cache_info"))
        {
            \_e(_("You need to install the APC PHP module to use this cache driver"));
        }

        $this_>log = Seringue::get_instance()->log_nano;
    }
    
    /**
     *
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return apc_fetch($name);
    }

    /**
     *
     * @param string $name
     * @param mixed $data
     */
    public function store($name, $data)
    {
        return apc_store($name, $data, $this->ttl);
    }

    /**
     *
     * @param string $name
     * @return bool
     */
    public function delete($name)
    {
        return apc_delete($name);
    }

    /**
     *
     * @param string $name
     * @return bool
     */
    public function check_for($name)
    {
        $this->log->addWarning("Please, don't use CacheApc->check_for() as it internally a call to CacheApc->get()");
    
        if($this->get($name))
        {
            $r = true;
        }
        else
        {
            $r = false;
        }
        
        return $r;
    }

    /**
     */
    public function clear_all()
    {
        return apc_clear_cache("user");
    }
}
