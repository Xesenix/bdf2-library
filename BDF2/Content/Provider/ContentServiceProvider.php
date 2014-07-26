<?php

namespace BDF2\Content\Provider
{
	use Silex\Application;
	use Silex\ServiceProviderInterface;
	
	class ContentServiceProvider implements ServiceProviderInterface {
		
		public function register(Application $app)
		{
			// Checking for dependencies
			if (!isset($app['orm.em'])) 
			{
				throw new \RuntimeException('You must register ORM EntityManager before registring  ' . get_class($this));
	        }
			
			// Setup routing
			$app['content.root'] = '/articles';
			
			$app['content.module_controller'] = $app->share(function() use($app) {
				$module = $app['controllers_factory'];
				
				$module->match('/', 'BDF2\\Content\Controllers\ArticleController::listAction')->bind('articles');
				$module->match('/{slug}', 'BDF2\\Content\Controllers\ArticleController::articleAction')->bind('article');
				
				return $module;
			});
			
			// Adding entities to ORM Entity Manager
			$app['orm.em.paths'] = $app->share($app->extend('orm.em.paths', function ($paths) use ($app) {
				$paths[] = array(__DIR__ . '/../Entity');
				
				return $paths;
			}));
		}

	    public function boot(Application $app)
	    {
			$app->mount($app['routes.content'], $app['content.module_controller']);
	    }
	}
}