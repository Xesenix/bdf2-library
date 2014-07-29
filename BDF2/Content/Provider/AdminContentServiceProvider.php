<?php

namespace BDF2\Content\Provider
{
	use Silex\Application;
	use Silex\ServiceProviderInterface;
	use Silex\ControllerProviderInterface;
	use BDF2\Content\Form\ArticleType;
	use BDF2\Content\Controllers\AdminArticleController;
	
	class AdminContentServiceProvider implements ServiceProviderInterface, ControllerProviderInterface {
		
		public function connect(Application $app)
		{
			$module = $app['controllers_factory'];
			
			$module->match('/', 'content.controllers.admin_article_controller:listAction')->bind('articles');
			$module->match('/{id}', 'content.controllers.admin_article_controller:editAction')->bind('article');
			
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
			$app['content.controllers.admin_article_controller'] = $app->share(function() use($app) {
				return new AdminArticleController($app);
			});
			
			// Setup routing
			$app['content.routes.admin_prefix'] = '/articles';
			
			/*$app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use ($app) {
				$extensions[] = new ArticleType();
				
				return $extensions;
			}));*/
			
			/*$app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
				$extensions[] = new ArticleType();
				
				return $extensions;
			}));*/
			
			// Adding entities to ORM Entity Manager
			$app['orm.em.paths'] = $app->share($app->extend('orm.em.paths', function ($paths) use ($app) {
				$paths[] = __DIR__ . '/../Entity';
				
				return $paths;
			}));
		}

	    public function boot(Application $app)
	    {
			$app->mount($app['content.routes.admin_prefix'], $this);
	    }
	}
}