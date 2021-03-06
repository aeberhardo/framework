<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AssetPublisher;
use Illuminate\Foundation\ConfigPublisher;
use Illuminate\Foundation\Console\AssetPublishCommand;
use Illuminate\Foundation\Console\ConfigPublishCommand;

class PublisherServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerAssetPublisher();

		$this->registerConfigPublisher();

		$this->commands('command.asset.publish', 'command.config.publish');
	}

	/**
	 * Register the asset publisher service and command.
	 *
	 * @return void
	 */
	protected function registerAssetPublisher()
	{
		$this->registerAssetPublishCommand();

		$this->app['asset.publisher'] = $this->app->share(function($app)
		{
			$publicPath = $app['path.public'];

			// The asset "publisher" is responsible for moving package's assets into the
			// web accessible public directory of an application so they can actually
			// be served to the browser. Otherwise, they would be locked in vendor.
			$publisher = new AssetPublisher($app['files'], $publicPath);

			$publisher->setPackagePath($app['path.base'].'/vendor');

			return $publisher;
		});
	}

	/**
	 * Register the asset publish console command.
	 *
	 * @return void
	 */
	protected function registerAssetPublishCommand()
	{
		$this->app['command.asset.publish'] = $this->app->share(function($app)
		{
			return new AssetPublishCommand($app['asset.publisher']);
		});
	}

	/**
	 * Register the configuration publisher class and command.
	 *
	 * @return void
	 */
	protected function registerConfigPublisher()
	{
		$this->registerConfigPublishCommand();

		$this->app['config.publisher'] = $this->app->share(function($app)
		{
			$configPath = $app['path'].'/config';

			// Once we have created the configuration publisher, we will set the default
			// package path on the object so that it knows where to find the packages
			// that are installed for the application and can move them to the app.
			$publisher = new ConfigPublisher($app['files'], $configPath);

			$publisher->setPackagePath($app['path.base'].'/vendor');

			return $publisher;
		});
	}

	/**
	 * Register the configuration publish console command.
	 *
	 * @return void
	 */
	protected function registerConfigPublishCommand()
	{
		$this->app['command.config.publish'] = $this->app->share(function($app)
		{
			return new ConfigPublishCommand($app['config.publisher']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'asset.publisher',
			'command.asset.publish',
			'config.publisher',
			'command.config.publish'
		);
	}

}
