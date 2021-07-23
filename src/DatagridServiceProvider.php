<?php

namespace Camohub\LaravelDatagrid;


use Camohub\LaravelDatagrid;
use Illuminate\Support\ServiceProvider;


/**
 * Class DatagridServiceProvider
 */
class DatagridServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('camohubLaravelDatagrid', LaravelDatagrid\Datagrid::class);
		$this->loadViewsFrom( __DIR__ . '/../resources/views', 'camohubLaravelDatagrid');
		$this->publishes([__DIR__ . '/../resources/views' => resource_path('views/vendor/camohubLaravelDatagrid')]);
	}
}