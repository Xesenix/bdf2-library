<?php

namespace BDF2\Content\Provider
{
	use Silex\Application;
	use Silex\ServiceProviderInterface;
	use Silex\ControllerProviderInterface;
	
	class ContentServiceProvider implements ServiceProviderInterface, ControllerProviderInterface {
		
		public function connect(Application $app)
		{
			$module = $app['controllers_factory'];
			
			$module->match('/', 'BDF2\\Content\Controllers\ArticleController::listAction')->bind('articles');
			$module->match('/{slug}', 'BDF2\\Content\Controllers\ArticleController::articleAction')->bind('article');
			
			return $module;
		}
		
		public function register(Application $app)
		{
			// Checking for dependencies
			if (!isset($app['orm.em'])) 
			{
				throw new \RuntimeException('You must register ORM EntityManager before registring  ' . get_class($this));
	        }
			
			// Setup routing
			$app['routes.content'] = '/articles';
			
			// Adding entities to ORM Entity Manager
			$app['orm.em.paths'] = $app->share($app->extend('orm.em.paths', function ($paths) use ($app) {
				$paths[] = __DIR__ . '/../Entity';
				
				return $paths;
			}));
		}

	    public function boot(Application $app)
	    {
			$app->mount($app['routes.content'], $this);
	    }
	}
}