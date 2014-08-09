<?php
namespace BDF2\Widget\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Widget\Controllers\WidgetController;
use BDF2\Widget\Entity\Widget;

class WidgetServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{

	public function connect(Application $app) {
		$module = $app['controllers_factory'];

		$module->match('/position/{position}', 'widget.controllers.widget_controller:renderPositionAction')->bind('widget:position');
		$module->match('/content/{widget}', 'widget.controllers.widget_controller:renderContentAction')->bind('widget:content')->convert('widget', function($widget) use ($app) {
			if ($widget != null)
			{
				$entityManager = $app['orm.em'];

				return $entityManager->getRepository('BDF2\Widget\Entity\Widget')->findOneById($widget);
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
		$app['widget.controllers.widget_controller'] = $app->share(function() use ($app) {
			return new WidgetController($app);
		});

		// Setup routing
		$app['widget.routes.prefix'] = '/widgets';

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
		$app->mount($app['widget.routes.prefix'], $this);

		/*$app->on('twig:render', function() use($app) {

		 $app['twig']->addGlobal('widgetManager', $app['widget.manager']);
		 });*/
	}

}
