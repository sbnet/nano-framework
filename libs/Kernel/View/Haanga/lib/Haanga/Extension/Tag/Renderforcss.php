<?php

class Haanga_Extension_Tag_Renderforcss
{
    public static $is_block  = false;

    /* This tag calls to a PHP native function */
    public static $php_alias = "NanoFramework\Kernel\Seringue::get_instance()->page->render_for_css"; 
}
