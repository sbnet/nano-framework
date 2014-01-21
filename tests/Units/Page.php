<?php
namespace Tests\Units\NanoFramework\Utilities;
use NanoFramework\Utilities;

require_once __DIR__.'/../Test.php';

class Page extends \Tests\Units\Test
{
   public function testTitle()
   {
        $page = new Utilities\Page(true);       
        $page->set_title("Titre");

        $this->assert
            ->string($page->render_for_title())->isEqualTo("<title>Titre</title>\n")
        ;
   }
}
