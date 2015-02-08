<?php namespace Flamingo\Provider;

use Route;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

    /**
     * Load module routers.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $moduleBase = $this->app->config['flamingo.modulePath'];

        foreach ($this->app->config['flamingo.modules'] as $module)
        {
            $routeFile = "{$moduleBase}/{$module}/routes.php";
            $moduleNamespace = camel_case($module);
            $namespace = "Module\\{$moduleNamespace}\\Controller";
            $payload = ['namespace' => $namespace];

            $router->group($payload, function($router) use ($routeFile) {
                require $routeFile;
            });
        }
    }

}
