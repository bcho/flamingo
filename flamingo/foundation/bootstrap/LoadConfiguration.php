<?php namespace Flamingo\Foundation\Bootstrap;

use Symfony\Component\Finder\Finder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Foundation\Bootstrap\LoadConfiguration as BaseLoadConfiguration;

/**
 * Load application & module configurations.
 */
class LoadConfiguration extends BaseLoadConfiguration {

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        // Load laravel configuration.
        parent::bootstrap($app);
        $config = $app->make('config');

        $this->prepareFlamingoConfig($app, $config);
        $this->loadModuleConfigs($app, $config);
        $this->mergeModuleServiceProviders($app, $config);
    }

    /**
     * Load flamingo configuration.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Contracts\Config\RepositoryContract $config
     * @return void
     */
    protected function prepareFlamingoConfig(
        Application $app,
        RepositoryContract $config
    ) {
        $config['flamingo'] = array_merge([
            'modulePath' => base_path() . 'module',
            'modules'    => [],
            'module'     => []
        ], $config->get('flamingo', []));
    }

    /**
     * Load module confingurations.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Contracts\Config\RepositoryContract $config
     * @return void
     * @todo Failed on import error.
     */
    protected function loadModuleConfigs(
        Application $app,
        RepositoryContract $config
    ) {
        $moduleBase = $config['flamingo.modulePath'];
        $modules = $config['flamingo.modules'];

        foreach ($modules as $module)
        {
            $configPath = "{$moduleBase}/{$module}/config";
            $configKeyPrefix = "flamingo.module.{$module}.";

            $finder = Finder::create()
                ->files()
                ->name('*.php')
                ->in($configPath);
            foreach ($finder as $file)
            {
                $name = basename($file->getRealPath(), '.php');
                $configKey = $configKeyPrefix . $name;
                $config->set($configKey, require $file->getRealPath());
            }
        }
    }

    /**
     * Merge module service providers into ``app.providers``.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Contracts\Config\RepositoryContract $appConfig
     * @return void
     */
    protected function mergeModuleServiceProviders(
        Application $app,
        RepositoryContract $appConfig
    ) {
        $providers = $appConfig['app.providers'];
        $configs = $appConfig['flamingo.module'];

        foreach ($configs as $config)
        {
            if (isset($config['provider']))
            {
                $providers = array_merge($providers, $config['provider']);
            }
        }

        $appConfig->set('app.providers', $providers);
    }

}
