<?php

/**
* url tag
*
* @author Stephane BRUN
*/
class Haanga_Extension_Tag_Url
{
    public static $is_block  = false;
   
    static function generator($cmp, $args)
    {
        if(count($args) == 0) 
        {
            $cmp->Error(_("url tag must have at least one parameter"));
        }
\_d($args);    
        $params = "";
        foreach($args as $arg)
        {
            $params .= $arg["string"];
        } 

        return hexec("\NanoFramework\Kernel\Route::get_instance()->url", $params);
    }    
}
