<?php

namespace BDF2\Content\Provider
{
	use Silex\Application;
	use Silex\ServiceProviderInterface;
	use BDF2\Content\Form\ArticleType;
	
	class AdminContentServiceProvider implements ServiceProviderInterface {
		
		public function register(Application $app)
		{
			// Checking for dependencies
			if (!isset($app['orm.em']))
			{
				throw new \RuntimeException('You must register ORM EntityManager before registring ' . get_class($this));
	        }
			
			// Setup routing
			$app['routes.content.admin'] = '/articles';
			
			$app['content.admin.module_controller'] = $app->share(function() use($app) {
				$module = $app['controllers_factory'];
				
				$module->match('/', 'BDF2\\Content\Controllers\AdminArticleController::listAction')->bind('articles');
				$module->match('/{id}', 'BDF2\\Content\Controllers\AdminArticleController::editAction')->bind('article');
				
				return $module;
			});
			
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
				$paths[] = array(__DIR__ . '/../Entity');
				
				return $paths;
			}));
		}

	    public function boot(Application $app)
	    {
			$app->mount($app['routes.content.admin'], $app['content.admin.module_controller']);
	    }
	}
}