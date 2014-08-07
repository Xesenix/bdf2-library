<?php
namespace BDF2\Navigation\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class NavigationServiceProvider implements ServiceProviderInterface
{

	public function register(Application $app) {
		// Checking for dependencies
		if (!isset($app['orm.em']))
		{
			throw new \RuntimeException('You must register ORM EntityManager before registring  ' . get_class($this));
		}

		// Adding entities to ORM Entity Manager
		$app['orm.em.paths'] = $app->share($app->extend('orm.em.paths', function($paths) use ($app) {
			$paths[] = __DIR__ . '/../Entity';

			return $paths;
		}));
	}

	public function boot(Application $app) {
	}
}
