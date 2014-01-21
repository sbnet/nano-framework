<?php
namespace Helpers;

/**
* Text manipulations class 
*
* @todo management of more exceptions
* @package Helpers
* @author Stephane BRUN
*/
class TextStorage
{
    protected static $_instance;

    protected $exceptions = array(
            'bras' => 'bras',
            'souris'=>'souris',
            'bijou'=>'bijoux',
            'chou'=>'choux',
            'caillou'=>'cailloux',
            'hibou' =>' hiboux',
            'pou' => 'poux',
            'genou'=>'genoux');
            
    public static function get_instance()
    {
        static $_instance;
        if(is_null(self::$_instance))
        {               
            $c = __CLASS__;
            self::$_instance = new $c;
        }
        return self::$_instance;
    }     

    /**
    * Give the purial of a word regarding the quantity
    * It can handle exceptions
    *
    * @param int $number quantity
    * @param string
    */
    public function pluralize($number, $word)
    {        
        if($number > 1)
        {
            if(array_key_exists($word, $this->exceptions))
            {
                return($this->exceptions[$word]);
            }
            else
            {
                return($word.'s');
            }
        }
        else
        {
            return($word);
        }
    }
    
    /**
    * Replace all the exceptions by the ones given in parameter
    *
    * It is usefull for web sites in other/multiple languages
    *
    * @param array $exceptions array('singuliar'>'plurial', ... );
    */
    public function set_exceptions($exceptions)
    {
        $this->exceptions = $exceptions;        
    }

    /**
    * Truncate a string
    *
    * @param string $text string to truncate
    * @param int $size final size
    * @param string $end will be appended to the final string
    * @return string
    */
    public function truncate($text, $size, $end='...')
    {
        if(strlen($text) > $size)
        {
          $text = substr(trim($text), 0, $size-strlen($end));
          $text = ereg_replace("\r\n",' ',$text);
        }
        return($text.$end);
    }

    /**
    * Shorten a string without cutting inside a word
    *
    * The returned string can be smaller than requested
    *
    * @param string $text string to process
    * @param int $taille requested size
    * @param string $end end of string terminator
    * @return string
    */
    public function shorten($text, $size, $end='...')
    {
        if(strlen($text) > $size)
        {
            $text = substr(trim($text),0,$size-strlen($end));
            $text = substr($text,0,strlen($text)-strpos(strrev($text),' '));
        }
        else
        {
            $end='';
        }
        $text = ereg_replace("\r\n",' ',$text);
        return $text.$end;
    }
    
    public function slug($text)
    {
        return \NanoFramework\Utilities\String::slug($text);
    }
}

/**
* Text helper class
*/
class Text
{
    static public function pluralize($number, $word)
    {   
        return TextStorage::get_instance()->pluralize($number, $word);
    }
    
    static public function set_exceptions($exceptions)
    {   
        TextStorage::get_instance()->set_exceptions($exceptions);
    }
    
    static public function truncate($text, $size, $end='...')
    {
        return TextStorage::get_instance()->truncate($text, $size, $end='...');
    }
        
    static public function shorten($text, $size, $end='...')
    {
        return TextStorage::get_instance()->shorten($text, $size, $end='...');
    }    

    static public function slug($text)
    {
        return TextStorage::get_instance()->slug($text);
    }
}
