<?php
namespace Experience\Map;

use Illuminate\Support\ServiceProvider;

class MapServiceProvider extends ServiceProvider
{
	protected $defer = false;


	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('experience/map');
	}


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['map.facade'] = $this->app->share(function($app)
		{
			return new Commands\MapFacadeCommand($app);
		});

		$this->commands('map.facade');
	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}
}
