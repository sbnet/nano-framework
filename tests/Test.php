<?php
namespace Tests\Units;

$APP_BASE = realpath(__DIR__."/../..");
$GLOBALS['env']['environment'] = "development";

// The env is ok, load the nano kernel   
if(file_exists("$APP_BASE/configuration/environments/{$GLOBALS['env']['environment']}.php"))
{
    include $APP_BASE.'/configuration/configuration.php';
}
else
{
    echo _("Wrong environment... {$GLOBALS['env']['environment']} doesn't exists !")."\n";
    exit(1);
}

// Load Atoum
use mageekguy\atoum;
require_once __DIR__.'/mageekguy.atoum.phar';

abstract class Test extends atoum\test
{
   public function __construct(score $score = null, locale $locale = null, adapter $adapter = null)
   {
      $this->setTestNamespace('Tests\Units');

      parent::__construct($score, $locale, $adapter);
   }
}