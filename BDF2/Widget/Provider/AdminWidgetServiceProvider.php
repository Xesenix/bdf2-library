<?php
namespace BDF2\Widget\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Widget\Entity\Widget;
use BDF2\Widget\Form\Type\WidgetType;
use BDF2\Controllers\RestController;

class AdminWidgetServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{
	protected $app = null;
	
	protected $moduleName = 'widget.widget_admin';
	
	public function connect(Application $app) {
		$module = $app['controllers_factory'];
		
		$module->match('/', $this->moduleName . '.controller:dashboardAction')->bind($this->moduleName . ':dashboard');
		$module->match('/widgets', $this->moduleName . '.controller:listAction')->bind($this->moduleName . ':list');
		$module->match('/widget/add', $this->moduleName . '.controller:addAction')->bind($this->moduleName . ':add');
		$module->match('/widget/remove/{resource}', $this->moduleName . '.controller:removeAction')->bind($this->moduleName . ':remove');
		$module->match('/widget/{resource}', $this->moduleName . '.controller:editAction')->bind($this->moduleName . ':edit');
		
		$module->convert('resource', $this->app[$this->moduleName . '.resource_provider']);
		
		return $module;
	}

	public function register(Application $app) {
		$moduleName = $this->moduleName;
		$this->app = $app;
		
		// Checking for dependencies
		if (!isset($app['orm.em']))
		{
			throw new \RuntimeException('You must register ORM EntityManager before registring ' . get_class($this));
		}
		
		// Setup controller provider
		$app[$moduleName . '.module_provider'] = $this;
		
		// Setup controllers
		$app[$moduleName . '.controller'] = $app->share(function() use ($app, $moduleName) {
			$em = $app['orm.em'];
			$moduleViewPath = str_replace('.', '/', $moduleName);
			
			return new RestController($app, $moduleName, $moduleViewPath, $em->getRepository('BDF2\Widget\Entity\Widget'), $app[$moduleName . '.form_provider']);
		});

		// Setup routing
		$app[$this->moduleName . '.routes.prefix'] = '/widgets';

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

		// Setup resources managed by module
		$app[$moduleName . '.resource_provider'] = $app->protect(function($id) use ($app) {
			if ($id != null)
			{
				$entityManager = $app['orm.em'];
		
				return $entityManager->getRepository('BDF2\Widget\Entity\Widget')->findOneById($id);
			}
		
			return null;
		});
		
		// Setup form
		$app[$this->moduleName . '.form_provider'] = $app->protect(function($widget) use ($app) {
			return $app['form.factory']->create(new WidgetType(), $widget);
		});
	}

	public function boot(Application $app) {
		$app->mount($app[$this->moduleName . '.routes.prefix'], $app[$this->moduleName . '.module_provider']);
	}

}
