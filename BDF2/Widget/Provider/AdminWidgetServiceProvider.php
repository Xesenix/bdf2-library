<?php
namespace BDF2\Widget\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Widget\Controllers\AdminWidgetController;
use BDF2\Widget\Entity\Widget;
use BDF2\Widget\Form\Type\WidgetType;

class AdminWidgetServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{

	public function connect(Application $app) {
		$module = $app['controllers_factory'];

		$module->match('/', 'widget.controllers.admin_widget_controller:dashboardAction')->bind('widget:admin:dashboard');
		$module->match('/widgets', 'widget.controllers.admin_widget_controller:listAction')->bind('widget:admin:widget:list');
		$module->match('/widget/add', 'widget.controllers.admin_widget_controller:addAction')->bind('widget:admin:widget:add');
		$module->match('/widget/remove/{id}', 'widget.controllers.admin_widget_controller:removeAction')->bind('widget:admin:widget:remove');
		$module->match('/widget/{id}', 'widget.controllers.admin_widget_controller:editAction')->bind('widget:admin:widget:edit');

		return $module;
	}

	public function register(Application $app) {
		// Checking for dependencies
		if (!isset($app['orm.em']))
		{
			throw new \RuntimeException('You must register ORM EntityManager before registring ' . get_class($this));
		}

		// Setup Controllers
		$app['widget.controllers.admin_widget_controller'] = $app->share(function() use ($app) {
			return new AdminWidgetController($app);
		});

		// Setup routing
		$app['widget.routes.admin_prefix'] = '/widgets';

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
		$app['widget.widget.form'] = $app->protect(function($widget) use ($app) {
			return $app['form.factory']->create(new WidgetType(), $widget);
		});
	}

	public function boot(Application $app) {
		$app->mount($app['widget.routes.admin_prefix'], $this);
	}

}
