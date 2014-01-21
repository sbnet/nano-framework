<?php
/**
* The memcache cache
*
* @package NanoFramework\Utilities\Cache
*/
namespace NanoFramework\Utilities\Cache;
use NanoFramework\Utilities;

include 'iCacheDriver.php';

/**
 * Memcache Cache class
 *
 * @author Stephane BRUN
 */
class Memcache implements iCacheDriver
{    
    public $ttl = 3600; 
    private $server;

    public function __construct($host="localhost", $port="11211")
    {
        if(!class_exists("Memcache"))
        {
            \_e(_("You need the memcache PHP module to use this cache driver"));
        }
        
        $this->server = new Memcache();  
        $this->connect($host, $port);     
        $this->log = Seringue::get_instance()->log_nano; 
    }
    
    private function connect($host, $port)
    {
        $this->server->connect($host, $port);
    }
    
    /**
     *
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->server->get($name);
    }

    /**
     *
     * @param string $name
     * @param mixed $data
     */
    public function store($name, $data)
    {
        return $this->server->set($name, $data, false, $this->ttl);
    }

    /**
     *
     * @param string $name
     * @return bool
     */
    public function delete($name)
    {
        return $this->server->delete($name);
    }

    /**
     *
     * @param string $name
     * @return bool
     */
    public function check_for($name)
    {
        $this-log->addWarning("Please, don't use CacheMemcache->check_for() as it internally call CacheMemcache->get()");
        
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
        return $this->server->flush();
    }
}
