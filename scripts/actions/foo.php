<?php
namespace Scripts;

/**
*
* @author Stéphane BRUN
*/
class FooTask implements iTask
{
    private $cli;
    
    public function __construct($cli)
    {
        $this->cli = $cli;
    }

    public function help()
    {
        $this->cli->out("This is the help\n");
    }
    
    public function run()
    {
        $this->cli->out("foo te salue\n");
    }
}

