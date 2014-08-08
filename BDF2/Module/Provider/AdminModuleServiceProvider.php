<?php
namespace BDF2\Module\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Module\Controllers\AdminModuleController;
use BDF2\Module\Entity\Module;
use BDF2\Module\Form\Type\ModuleType;

class AdminModuleServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{

	public function connect(Application $app) {
		$module = $app['controllers_factory'];

		$module->match('/', 'module.controllers.admin_module_controller:dashboardAction')->bind('module:admin:dashboard');
		$module->match('/modules', 'module.controllers.admin_module_controller:listAction')->bind('module:admin:module:list');
		$module->match('/module/add', 'module.controllers.admin_module_controller:addAction')->bind('module:admin:module:add');
		$module->match('/module/remove/{id}', 'module.controllers.admin_module_controller:removeAction')->bind('module:admin:module:remove');
		$module->match('/module/{id}', 'module.controllers.admin_module_controller:editAction')->bind('module:admin:module:edit');

		return $module;
	}

	public function register(Application $app) {
		// Checking for dependencies
		if (!isset($app['orm.em']))
		{
			throw new \RuntimeException('You must register ORM EntityManager before registring ' . get_class($this));
		}

		// Setup Controllers
		$app['module.controllers.admin_module_controller'] = $app->share(function() use ($app) {
			return new AdminModuleController($app);
		});

		// Setup routing
		$app['module.routes.admin_prefix'] = '/modules';

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

		// Setup form
		$app['module.module.form'] = $app->protect(function($module) use ($app) {
			return $app['form.factory']->create(new ModuleType(), $module);
		});
	}

	public function boot(Application $app) {
		$app->mount($app['module.routes.admin_prefix'], $this);
	}

}
