<?php
namespace Scripts;

/**
*
* @author Stéphane BRUN
*/
class FixpermsTask implements iTask
{
    private $cli;
    
    public function __construct($cli)
    {
        $this->cli = $cli;
    }

    public function run()
    {
        $directories = array('logs', 'public/uploads', 'temp', 'temp/cache');   
        $this->fixem($directories);
    }
    
    public function help()
    {    
    }

    private function fixem($directories)
    {
        foreach($directories as $dir)
        {
            $this->cli->out("Fixing: ".DIR_APP.$dir."\n");

            chmod(DIR_APP.$dir, 0777);
        } 
    }
}

