<?php
namespace BDF2\Navigation\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Navigation\Controllers\AdminMenuController;
use BDF2\Navigation\Form\Type\MenuType;

class AdminNavigationServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{

	public function connect(Application $app) {
		$module = $app['controllers_factory'];

		$module->match('/menus', 'navigation.controllers.admin_menu_controller:listAction')->bind('navigation:admin:menu:list');
		$module->match('/menu/add', 'navigation.controllers.admin_menu_controller:addAction')->bind('navigation:admin:menu:add');
		$module->match('/menu/remove/{id}', 'navigation.controllers.admin_menu_controller:removeAction')->bind('navigation:admin:menu:remove');
		$module->match('/menu/{id}', 'navigation.controllers.admin_menu_controller:editAction')->bind('navigation:admin:menu:edit');

		return $module;
	}

	public function register(Application $app) {
		// Checking for dependencies
		if (!isset($app['orm.em']))
		{
			throw new \RuntimeException('You must register ORM EntityManager before registring  ' . get_class($this));
		}
		
		// Setup Controllers
		$app['navigation.controllers.admin_menu_controller'] = $app->share(function() use ($app) {
			return new AdminMenuController($app);
		});

		// Setup routing
		$app['navigation.routes.admin_prefix'] = '/navigation';

		// Adding entities to ORM Entity Manager
		$app['orm.em.paths'] = $app->share($app->extend('orm.em.paths', function($paths) use ($app) {
			$paths[] = __DIR__ . '/../Entity';

			return $paths;
		}));
		
		// Setup form
		$app['navigation.menu.form'] = $app->protect(function($menu) use ($app) {
			return $app['form.factory']->create(new MenuType(), $menu);
		});

		// Adding view paths
		$app['twig.path'] = $app->share($app->extend('twig.path', function($paths) {
			$paths[] = __DIR__ . '/../views';

			return $paths;
		}));
	}

	public function boot(Application $app) {
		$app->mount($app['navigation.routes.admin_prefix'], $this);
	}
}
