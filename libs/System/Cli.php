<?php
namespace NanoFramework\System;

/**
* Command Line Interface
*
* @package NanoFramework\System
* @author Stéphane BRUN
* @version 0.0.1 
*/
class Cli extends ConsoleIo
{
    public $argc;
    public $argv;
    public $last_error;
    public $last_output;        
    public $my_name;
    public $my_path;
    
    /**
    *
    * @param $c $argc
    * @param $v $argv
    */
    function __construct($c, $v)
    {
        // Runs only fron the cli
        if(php_sapi_name() != 'cli')
        {
            exit(1);
        }
               
        $this->argc = $c;
        $this->argv = $v;
        
        $this->my_name = basename($this->argv[0]);
        $this->my_path = dirname($this->argv[0]);
        unset($this->argv[0]); 
        $this->argc--;       
        
        parent::__construct();
    }
    
    /**
    * Hook to run commands directly
    *
    * The result is stored into the 'last_output' public property
    * The error code is stored into the 'last_error' public property
    *
    * Example :
    * <code>
    *  $cli->ls;               // Simply runs a 'ls'
    *  $cli->ls('-al');        // runs a 'ls -al'
    *  $cli->ls('-al', false); // runs a 'ls -al' and don't show the output
    * </code>
    *
    * @param $name string name of the method
    * @param $arguments string 
    * @return string the output of the command
    */
    public function __call($name, $arguments)
    {
        $args = empty($arguments[0])?null:$arguments[0];
        $show=true;
        if(isset($arguments[1]))
        {
            $show = $arguments[1]===null?true:$arguments[1];
        }
        return $this->execute($name.' '.$args, $show);
    }

    /**
    * Executes a command
    *
    * The result is stored into the 'last_output' public property
    * The error code is stored into the 'last_error' public property
    *
    * @param $command string the command to run
    * @param $show bool if false no output to the screen else show the result
    * @return string the output of the command
    */
    public function execute($command, $show=true)
    {
        ob_start();
        passthru($command, $this->last_error);
        $this->last_output = ob_get_contents();
        ob_end_clean();

        if($show)
        {
            $this->out($this->last_output);
        }
        
        return $this->last_output;
    }    
        
    /**
    * Retreive the value of a given argument passed on the command line.
    *
    * The last char is used as a separator to get the value of the argument.
    *
    * @param $arguments int    number of the argument to retreive (1 is the first one)
    * @param $arguments string name of the argument to retreive the value of (Ex: "--verbose=" or "-v ")
    * @todo handle array of arguments
    */
    public function parameter_value($arguments)
    {
        $value = null;
        
        // Par indice ?
        if(is_numeric($arguments))
        {
            if(isset($this->argv[$arguments]))
            {
                $value = $this->argv[$arguments];
            }
        }
        else
        {
            if(is_array($arguments))
            {
            }
            else
            {
                $argument = substr($arguments, 0, strlen($arguments)-1);
                $separator = substr($arguments, -1);
                
                $value = $this->one_parameter_value($argument, $separator);
            }
        }    
        return $value;
    }

    /**
    * Checks if an argument exists
    *
    * @param mixed $arguments one or more parameters
    * @return bool
    */
    public function parameter_exists($arguments)
    {
        $exists = false;
        
        if(!is_array($arguments))
        {
            $arguments = array($arguments);
        }
        
        foreach($this->argv as $indice=>$argument)
        {
            $argument = preg_replace('/=(.*)/i', '', $argument);
            if(in_array($argument, $arguments))
            {
                $exists = true;
                break;
            }
        }
    
        return $exists;
    }

    public function rmdir($dir)
    {
        if(!$dh = opendir($dir))
        {
            return;
        }

        while($obj=readdir($dh))
        {
            if($obj=='.' || $obj=='..')
            {
                continue;
            }

            if(!unlink($dir.'/'.$obj))
            {
                $this->rmdir($dir.'/'.$obj);
            }
        }
        closedir($dh);
        rmdir($dir);
    }

    /**
    * Create a new directory with 755 rights, it can recurse across the full path
    *
    * @param string path to create
    */
    public function create_dir($path)
    {
        return mkdir($path, 0755, true);
    }

    private function one_parameter_value($argument, $separator)
    {
        $value = null;
        if(count($this->argv))
        {
            foreach($this->argv as $indice=>$arg)
            {
                if(strpos($arg, $argument.$separator) === 0)
                {        
                    list($boum, $value) = explode($separator, $arg);
                    break;
                }
            }    
        }
        return $value;
    }    
}
