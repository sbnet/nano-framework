<?php
namespace Scripts;

/**
* !! STILL TO DEFINE !!
*
* @author Stéphane BRUN
*/
class InstallPluginTask implements iTask
{
    private $cli;
    private $install;
    
    public function __construct($cli)
    {
        $this->cli = $cli;
    }

    public function run()
    {
        $plugin_name = $this->cli->parameter_value(2);
        
        if($plugin_name)
        {
            if(is_dir(DIR_PLUGINS.$plugin_name))
            {
                $this->cli->out("Plugin's name : $plugin_name\n");
                if(is_file(DIR_PLUGINS.$plugin_name.'/install.php'))
                {
                    include_once(DIR_PLUGINS.$plugin_name.'/install.php');
                    $taskname = "Install{$plugin_name}Task";
                    $this->install = new $taskname($this->cli);
                    $this->install->set_name($plugin_name);
                    
                    if(!$this->install->run())
                    {
                        $this->cli->out(_("Plugin's installation failed !")."\n");        
                    }
                    else
                    {
                        $this->cli->out(_("Plugin installed, please refer to the manual for eventual 'by hand' tuning")."\n");        
                    }
                }
                else
                {
                    $this->cli->out(_('install.php not found !')."\n");        
                }
            }
            else
            {
                $this->cli->out(_("Plugin directory not found")."\n");        
            }
        }
        else
        {
            $this->cli->out(_("You must tell me the name of the plugin to install : nanophp install-plugin <plugin name>")."\n");        
        }
    }
}

