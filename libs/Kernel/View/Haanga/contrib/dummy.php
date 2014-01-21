<?php

Class Haanga_Extension_Tag_Dummy
{
    public $is_block = false;

    static function main($html)
    {
        return strtolower($html);
    }
}
