<?php
namespace BDF2\Module\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Module\Controllers\ModuleController;
use BDF2\Module\Entity\Module;

class ModuleServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{

	public function connect(Application $app) {
		$module = $app['controllers_factory'];

		$module->match('/position/{position}', 'module.controllers.module_controller:renderPositionAction')->bind('module:position');
		$module->match('/content/{module}', 'module.controllers.module_controller:renderContentAction')->bind('module:content')->convert('module', function($module) use ($app) {
			if ($module != null)
			{
				$entityManager = $app['orm.em'];

				return $entityManager->getRepository('BDF2\Module\Entity\Module')->findOneById($module);
			}

			return null;
		});

		return $module;
	}

	public function register(Application $app) {
		// Checking for dependencies
		if (!isset($app['orm.em']))
		{
			throw new \RuntimeException('You must register ORM EntityManager before registring ' . get_class($this));
		}

		// Setup Controllers
		$app['module.controllers.module_controller'] = $app->share(function() use ($app) {
			return new ModuleController($app);
		});

		// Setup routing
		$app['module.routes.prefix'] = '/modules';

		// Adding entities to ORM Entity Manager
		$app['orm.em.paths'] = $app->share($app->extend('orm.em.paths', function($paths) use ($app) {
			$paths[] = __DIR__ . '/../Entity';

			return $paths;
		}));

		// Adding view paths
		$app['twig.path'] = $app->share($app->extend('twig.path', function($paths) {
			$paths[] = __DIR__ . '/../views';

			return $paths;
		}));
	}

	public function boot(Application $app) {
		$app->mount($app['module.routes.prefix'], $this);

		/*$app->on('twig:render', function() use($app) {

		 $app['twig']->addGlobal('moduleManager', $app['module.manager']);
		 });*/
	}

}
