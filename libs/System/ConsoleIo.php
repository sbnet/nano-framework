<?php
namespace NanoFramework\System;

/**
* Console Input/Output
*
* @package NanoFramework\System
* @class ConsoleIo
* @author Stéphane BRUN <stephane.sbnet@gmail.com>
* @version 0.0.1
* @version 0.0.2 Works with windows 
*/
class ConsoleIo
{
    private $stdin;
    private $stdout;
    private $stderr;
    private $windows_os;

    private $background_colors = array(
        'LIGHT_RED'     => "[1;41m",
        'LIGHT_GREEN'   => "[1;42m",
        'YELLOW'        => "[1;43m",
        'LIGHT_BLUE'    => "[1;44m",
        'MAGENTA'       => "[1;45m",
        'LIGHT_CYAN'    => "[1;46m",
        'WHITE'         => "[1;47m",
        'BLACK'         => "[0;40m",
        'RED'           => "[0;41m",
        'GREEN'         => "[0;42m",
        'BROWN'         => "[0;43m",
        'BLUE'          => "[0;44m",
        'CYAN'          => "[0;46m"
    );
    private $front_colors = array(
        'LIGHT_RED'     => "[1;31m",
        'LIGHT_GREEN'   => "[1;32m",
        'YELLOW'        => "[1;33m",
        'LIGHT_BLUE'    => "[1;34m",
        'MAGENTA'       => "[1;35m",
        'LIGHT_CYAN'    => "[1;36m",
        'WHITE'         => "[1;37m",
        'BLACK'         => "[0;30m",
        'RED'           => "[0;31m",
        'GREEN'         => "[0;32m",
        'BROWN'         => "[0;33m",
        'BLUE'          => "[0;34m",
        'CYAN'          => "[0;36m"
    );
    private $styles = array(   
        'RESET'         => "[0m",        
        'NORMAL'        => "[0m",
        'BOLD'          => "[1m",
        'UNDERSCORE'    => "[4m",
        'REVERSE'       => "[7m"
    );
    
    function __construct()
    {
        $this->windows_os = (strtoupper(substr(PHP_OS,0,3)) == 'WIN');
        
        ini_set('html_errors', false); 
        
        $this->stdin = fopen("php://stdin","r");
        $this->stdout = fopen("php://stdin","w");
        $this->stderr = fopen("php://stdin","w");
    }
    
    /**
    * Read a string from the input
    */
    public function read($length='255')
    {       
       $line = fgets($this->stdin, $length);
       return trim($line);
    }

    /**
    * Write to the standard output, it is highly recommended to use this method instead of 'echo'
    * so pipes and redirections will work with your scripts
    *
    * @todo combined colors, front and back at the same time doesn't works
    */
    public function out($string, $color='', $background='', $style='')
    {
        if($this->windows_os)
        {
            echo $string;        
        } 
        else 
        {
            $this->set_style($style);
            $this->set_color($color); 
            $this->set_background($background); 
            fwrite($this->stdout, $string); 
        }    
    }

    /**
    * write to the error output
    */
    public function err($string, $color='', $background='', $style='')
    {
        if($this->windows_os)
        {
            echo $string;        
        } 
        else 
        {
            $this->set_style($style);
            $this->set_color($color); 
            $this->set_background($background); 
            fwrite($this->stderr, $string); 
        }
    }

    /**
    *
    */
    public function set_color($color)
    {    
        if($color)
        {
            fwrite($this->stdout, chr(27).$this->front_colors[$color]); 
        }
    }

 
    /**
    *
    */
    public function set_background($color)
    {
        if($color)
        {
            fwrite($this->stdout, chr(27).$this->background_colors[$color]); 
        }
    }

    /**
    *
    * @todo debug RESET
    */
    public function set_style($style)
    {
        if($style)
        {
            switch($style)
            {
                case 'RESET':
                    $this->execute('tput sgr0');
                break;

                default:
                    fwrite($this->stdout, chr(27).$this->styles[$style]); 
                break;
            }
        }
    }

    /**
    *
    */
    public function clr()
    {
        $this->execute('clear');
    }    
}
