<?php
/**
* @package NanoFramework\Utilities
* @author BRUN Stéphane <stephane@sbnet.fr>
*/
use NanoFramework\Utilities;

/**
* Debug
*/
function _d($var) 
{
	Seringue::get_instance()->log_nano->addDebug($var);
}

/**
* Info
*/
function _i($message) 
{
    Seringue::get_instance()->log_nano->addNotice($message);
}

/**
* Error
*/
function _e($var) 
{
    throw new NanoFramework\Kernel\NanoException(_($var), E_ERROR);
}

/**
* Warning
*/
function _w($message) 
{
    /*
    $db = debug_backtrace();
    $file = $db[1]['file'];
    $line = $db[1]['line'];
    
    Utilities\Log::get_instance()->warning("[IN $file AT LINE $line] : $message");
    */
}
