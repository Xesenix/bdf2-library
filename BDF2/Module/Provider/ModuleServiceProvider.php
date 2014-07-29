<?php
namespace BDF2\Module\Provider
{
	use Silex\Application;
	use Silex\ServiceProviderInterface;
	use Silex\ControllerProviderInterface;
	use BDF2\Module\Controllers\ModuleController;
	
	class ModuleServiceProvider implements ServiceProviderInterface, ControllerProviderInterface {
		
		public function connect(Application $app)
		{
			$module = $app['controllers_factory'];
			
			$module->match('/', 'module.controllers.module_controller:dashboardAction')->bind('module:dashboard');
			$module->match('/install', 'module.controllers.module_controller:installAction')->bind('module:add');
			
			return $module;
		}
		
		public function register(Application $app)
		{
			// Checking for dependencies
			if (!isset($app['orm.em'])) 
			{
				throw new \RuntimeException('You must register ORM EntityManager before registring  ' . get_class($this));
	        }
			
			// Setup Controllers
			$app['module.controllers.module_controller'] = $app->share(function() use($app) {
				return new ModuleController($app);
			});
						
			// Setup routing
			$app['module.routes.prefix'] = '/modules';
			
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
			$app->mount($app['module.routes.prefix'], $this);
	    }
	}
}
