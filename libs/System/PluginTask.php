<?php
namespace NanoFramework\System;

/**
* Plugin task
*
* !! STILL TO DEFINE !!
*
* @package NanoFramework\System
* @author Stephane BRUN
*/
class PluginTask
{
    protected $cli;
    private $plugin_name;
    
    public function __construct($cli)
    {
        $this->cli = $cli;
    }

    public function set_name($name)
    {
        $this->plugin_name = $name;
    }    
}    
