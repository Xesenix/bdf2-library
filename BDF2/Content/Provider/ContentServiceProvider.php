<?php

namespace BDF2\Content\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Content\Controllers\ArticleController;

class ContentServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{

	public function connect(Application $app) {
		$module = $app['controllers_factory'];

		$module->match('/', 'content.article.controller:listAction')->bind('articles');
		$module->match('/{slug}', 'content.article.controller:articleAction')->bind('article');

		return $module;
	}

	public function register(Application $app) {
		// Checking for dependencies
		if (!isset($app['orm.em']))
		{
			throw new \RuntimeException('You must register ORM EntityManager before registring ' . get_class($this));
		}

		// Setup Controllers
		$app['content.article.controller'] = $app->share(function() use ($app) {
			return new ArticleController($app);
		});

		// Setup routing
		$app['content.article.routes.prefix'] = '/articles';

		// Adding entities to ORM Entity Manager
		$app['orm.em.paths'] = $app->share($app->extend('orm.em.paths', function($paths) use ($app) {
			$paths[] = __DIR__ . '/../Entity';

			return $paths;
		}));

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
		$app->mount($app['content.article.routes.prefix'], $this);
	}

}
