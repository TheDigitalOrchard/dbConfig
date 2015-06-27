<?php namespace Terbium\DbConfig;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\FileLoader;

class DbConfigServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
	        // publish migrations during vendor:publish
		    realpath(__DIR__.'/../../migrations') => $this->app->databasePath().'/migrations',
		]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app['db-config'] = $this->app->share(
            function ($app) {

                $table = $app['config']['db-config::table'];

                $dbProvider = new DbProvider($table);

                return new DbConfig($app['config'], $app->environment(), $dbProvider);
            }
        );

        $this->app->booting(
            function () {

                $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                $loader->alias('DbConfig', 'Terbium\DbConfig\Facades\DbConfig');
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('db-config');
    }
}
