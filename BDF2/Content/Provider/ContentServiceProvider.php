<?php

namespace BDF2\Content\Provider
{
	use Silex\Application;
	use Silex\ServiceProviderInterface;
	use Silex\ControllerProviderInterface;
	use BDF2\Content\Controllers\ArticleController;
	
	class ContentServiceProvider implements ServiceProviderInterface, ControllerProviderInterface {
		
		public function connect(Application $app)
		{
			$module = $app['controllers_factory'];
			
			$module->match('/', 'content.controllers.article_controller:listAction')->bind('articles');
			$module->match('/{slug}', 'content.controllers.article_controller:articleAction')->bind('article');
			
			return $module;
		}
		
		public function register(Application $app)
		{
			// Checking for dependencies
			if (!isset($app['orm.em'])) 
			{
				throw new \RuntimeException('You must register ORM EntityManager before registring ' . get_class($this));
	        }
			
			// Setup Controllers
			$app['content.controllers.article_controller'] = $app->share(function() use($app) {
				return new ArticleController($app);
			});
			
			// Setup routing
			$app['content.routes.prefix'] = '/articles';
			
			// Adding entities to ORM Entity Manager
			$app['orm.em.paths'] = $app->share($app->extend('orm.em.paths', function ($paths) use ($app) {
				$paths[] = __DIR__ . '/../Entity';
				
				return $paths;
			}));
			
			// Adding view paths
			$app['twig.path'] = $app->share($app->extend('twig.path', function ($paths) {
				$paths[] = __DIR__ . '/../views';
				
				return $paths;
			}));
		}

	    public function boot(Application $app)
	    {
			$app->mount($app['content.routes.prefix'], $this);
	    }
	}
}