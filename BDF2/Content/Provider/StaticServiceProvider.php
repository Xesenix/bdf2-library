<?php

namespace BDF2\Content\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Content\Controllers\StaticController;

class StaticServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{

	public function connect(Application $app) {
		$module = $app['controllers_factory'];

		$module->match('/{page}', 'content.static.controller:page')->bind('page');

		return $module;
	}

	public function register(Application $app) {
		$app['content.static.module_provider'] = $this;
		
		// Setup Controllers
		$app['content.static.controller'] = $app->share(function() use ($app) {
			return new StaticController($app);
		});

		// Setup routing
		$app['content.static.routes.prefix'] = '/';

		// Adding view paths
		$app['twig.path'] = $app->share($app->extend('twig.path', function($paths) {
			$path = __DIR__ . '/../views';
			
			if (!in_array($path, $paths))
			{
				$paths[] = $path;
			}

			return $paths;
		}));
	}

	public function boot(Application $app) {
		$app->mount($app['content.static.routes.prefix'], $app['content.static.module_provider']);
	}

}
