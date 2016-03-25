<?php namespace Sanatorium\Analytics\Providers;

use Cartalyst\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class AnalyticsServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		$this->registerSpatieAnalyticsPackage();

		define('SANATORIUM_ANALYTICS', true);

		$this->prepareResources();

		// Register all the default hooks
        $this->registerHooks();
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{

	}

	/**
	 * Register cartalyst/cart package
	 * @return
	 */
	protected function registerSpatieAnalyticsPackage() {
		$serviceProvider = 'Spatie\LaravelAnalytics\LaravelAnalyticsServiceProvider';

		if (!$this->app->getProvider($serviceProvider)) {
			$this->app->register($serviceProvider);
			AliasLoader::getInstance()->alias('LaravelAnalytics', 'Spatie\LaravelAnalytics\LaravelAnalyticsFacade');
		}
	}

	/**
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        $config = realpath(__DIR__.'/../../config/config.php');

        $this->mergeConfigFrom($config, 'sanatorium-analytics');

        $this->publishes([
            $config => config_path('sanatorium-analytics.php'),
        ], 'config');
    }

    /**
     * Register all hooks.
     *
     * @return void
     */
    protected function registerHooks()
    {
        $hooks = [
            [
            	'position' => 'scripts.footer',
            	'hook' => 'sanatorium/analytics::hooks.trackingCode',
            ],
            [
            	'position' => 'admin.scripts.footer',
            	'hook' => 'sanatorium/analytics::hooks.trackingCodeAdmin',
            ],
        ];

        $manager = $this->app['sanatorium.hooks.manager'];

        foreach ($hooks as $item) {
        	extract($item);
            $manager->registerHook($position, $hook);
        }
    }

}
