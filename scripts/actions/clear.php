<?php
namespace Scripts;

/**
*
* @author Stéphane BRUN
*/
class ClearTask implements iTask
{
    private $cli;
    
    public function __construct($cli)
    {
        $this->cli = $cli;
    }

    public function help()
    {
        $this->cli->out(_("This task clear the cache system")."\n");
    }
    
    public function run()
    {
        $this->cli->out(_("Clearing the cache : "));
        
        if(is_dir(DIR_CACHE))
        {
            $scan = glob(rtrim(DIR_CACHE,'/').'/*');
            foreach($scan as $path)
            {
                if(is_dir($path))
                {
                    $this->cli->rmdir($path);
                }
                else
                {
                    unlink($path);
                }
            }
        }
        
        $this->cli->out(_("done")."\n");
    }
}

