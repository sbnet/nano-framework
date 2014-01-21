<?php
namespace Scripts;

/**
* Create a new module
*
* @author Stéphane BRUN
*/
class CreateModuleTask implements iTask
{
    private $cli;
    
    public function __construct($cli)
    {
        $this->cli = $cli;
    }

    public function help()
    {
    }
    
    public function run()
    {    
        $module = $this->cli->parameter_value(2);

        if($module)
        {
            if(!is_dir(DIR_MODULES.$module))
            {
                $this->cli->out(_("Creating a new module : $module")."\n");
                  
                // Create the directory structure
                $this->cli->out(_("+ Creating the path structure : "));          
                $this->cli->create_dir(DIR_MODULES.$module.'/controllers');
                $this->cli->create_dir(DIR_MODULES.$module.'/locales');
                $this->cli->create_dir(DIR_MODULES.$module.'/views/layouts');
                $this->cli->out(_('Ok'."\n"));
                
                // Create the index file
                $index  = "<?php\n/**\n*\n* @author \n*/\n";
                $index .= "use NanoFramework\\Kernel;\n\n\$GLOBALS['env']['MODULE_NAME'] = '$module';\n\n";
                $index .= "require_once('../configuration/configuration.php');\n\nKernel\\Dispatcher::get_instance()->dispatch();\n";
                
                if(file_put_contents(DIR_PUBLIC.'index-'.$module.'.php', $index))
                {
                    $this->cli->out(_("+ Index file written")."\n");
                    $this->cli->out(_("+ Don't forget to modify the public/.htaccess file")."\n", 'RED');
                    $this->cli->out("\n", '', '', 'NORMAL'); 
                }
                else
                {
                    $this->cli->err(_("Can't create the index file. Please investigate, there's maybe an acces right problem...")."\n");
                }
            }
            else
            {
                $this->cli->err(_("The module $module alreay exists !")."\n");
            }
        }
        else
        {
            $this->cli->err(_("You must tell me the module's name : nanophp create-module <module name>")."\n");        
        }
    }
}

