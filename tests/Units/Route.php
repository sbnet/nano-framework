<?php
namespace Tests\Units\NanoFramework\Kernel;
use NanoFramework\Kernel;

require_once __DIR__.'/../Test.php';

class Route extends \Tests\Units\Test
{
    public function test_singleton()
    {
        $this->assert
                ->object(Kernel\Route::get_instance())
                    ->isInstanceOf('\NanoFramework\Kernel\Route')
                    ->isIdenticalTo(Kernel\Route::get_instance());
    }

    public function test_get_route_config()
    {
        $this->assert
            ->array(Kernel\Route::get_instance()->get_route_config())                
                ->isNotEmpty();
    }

    public function test_route_decode()
    {
        $GLOBALS['env']['MODULE_NAME'] = "front";        
        $this->init_route();

        // The parameters are not processed here, we are not on a web environment so the $_GET, $_POST and $_FILES are not set.
        $route = Kernel\Route::get_instance()->route_decode("/abc/5/test.html?param=val&param2=val2");

        $this->assert
            ->array($route)  
                ->hasSize(5)              
                ->isNotEmpty()
            ->string($route["module"])
                ->isEqualTo("front")
            ->string($route["controller"])
                ->isEqualTo("\\Controllers\\Accueil")
            ->string($route["action"])
                ->isEqualTo("test")
            ->string($route["categorie"])
                ->isEqualTo("abc")
            ->string($route["page"])
                ->isEqualTo("5");                                        
    }

    public function test_url()
    {
        $this->init_route();

        $this->assert
            ->string(Kernel\Route::get_instance()->url("test?categorie=abc&page=5", "front"))
                ->isEqualTo("/abc/5/test.html");
    }

    public function test_get_404()
    {
        $this->init_route();

        $this->assert
            ->string(Kernel\Route::get_instance()->get_404("front"))
                ->isEqualTo("Accueil/notfound")
            ->variable(Kernel\Route::get_instance()->get_404())
                ->isNull();
    }

    private function init_route()
    {
        $route = array(
            'front' => array(    
                'routes' => array(
                    'test' => array(
                        'route'=>'/:categorie/:page/test.html', 
                        'controller'=>'Accueil', 
                        'action'=>'test'
                    ),
                    'default' => array('route'=>'/:controller/:action'),
                    'default_small' => array('route'=>'/:controller'),
                ),
                'default_controller'=> 'Accueil',
                'default_action' => 'index',
                'not_found' => 'Accueil/notfound'        
            ),
        );        
        Kernel\Route::get_instance()->set_route_config($route);
    }
}