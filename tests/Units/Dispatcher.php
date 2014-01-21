<?php
namespace Tests\Units\NanoFramework\Kernel;
use NanoFramework\Kernel;

require_once __DIR__.'/../Test.php';

class Dispatcher extends \Tests\Units\Test
{
    public function testSingleton()
    {
        $this->assert
                ->object(Kernel\Dispatcher::get_instance())
                    ->isInstanceOf('\NanoFramework\Kernel\Dispatcher')
                    ->isIdenticalTo(Kernel\Dispatcher::get_instance());
    }

    public function testController()
    {
        $d = Kernel\Dispatcher::get_instance();       
        $d->set_controller("controller");
        $d->set_action("action");

        $this->assert->string($d->get_controller())->isEqualTo("controller");
        $this->assert->string($d->get_action())->isEqualTo("action");
    }

    public function testPartial()
    {
    }

    public function testRedirectTo()
    {
    }

    public function testForwardTo()
    {
    }

    public function testForward404()
    {
    }
}
