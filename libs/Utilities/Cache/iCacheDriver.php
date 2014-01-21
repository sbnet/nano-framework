<?php 
/**
* The cache driver interface
*
* @package NanoFramework\Utilities\Cache
*/
namespace NanoFramework\Utilities\Cache;

/**
* Interface for the cache drivers
*
* @author BRUN Stéphane <stephane@sbnet.fr>
*/
interface iCacheDriver
{
    public function store($name, $data);
    public function get($name);
    public function delete($name);
    public function check_for($name);
    public function clear_all();    
}

