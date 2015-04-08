<?php

namespace BDF2\Content\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Content\Form\Type\CategoryType;
use BDF2\Controllers\RestController;

class AdminCategoryRestServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{
	protected $app = null;
	
	protected $moduleName = 'content.category_admin';
	
	public function connect(Application $app) {
		$module = $app['controllers_factory'];
		
		$this->addAdminBasicRouting($module);
		$this->addAdminHistoryRouting($module);
		$this->addBasicConverters($module);
		
		return $module;
	}
	
	public function addAdminBasicRouting(&$module)
	{
		$module->match('/', $this->moduleName . '.controller:listAction')->bind($this->moduleName . ':list');
		$module->match('/add', $this->moduleName . '.controller:addAction')->bind($this->moduleName . ':add');
		$module->match('/remove/{resource}', $this->moduleName . '.controller:removeAction')->bind($this->moduleName . ':remove');
		$module->match('/{resource}', $this->moduleName . '.controller:editAction')->bind($this->moduleName . ':edit');
	}
	
	public function addAdminHistoryRouting(&$module)
	{
		$module->match('/history/{resource}', $this->moduleName . '.controller:historyAction')->bind($this->moduleName . ':history');
		$module->match('/{resource}v{version}', $this->moduleName . '.controller:revertAction')->bind($this->moduleName . ':revert');
	}
	
	public function addBasicConverters(&$module)
	{
		$module->convert('resource', $this->app[$this->moduleName . '.resource_provider']);
		$module->assert('resource', '\d+');
		$module->assert('version', '\d+');
	}
	
	public function addBasicMiddlewares(&$module)
	{
		
	}

	public function register(Application $app) {
		$moduleName = $this->moduleName;
		$this->app = $app;
		
		$app[$moduleName . '.module_provider'] = $this;
		
		// Checking for dependencies
		if (!isset($app['orm.em']))
		{
			throw new \RuntimeException('You must register ORM EntityManager before registring ' . get_class($this));
		}
		
		if (!isset($app['twig']))
		{
			throw new \RuntimeException('You must register Twig before registring ' . get_class($this));
		}
		
		// Setup controller provider
		$app[$moduleName . '.module_provider'] = $this;
		
		// Setup controllers
		$app[$moduleName . '.controller'] = $app->share(function() use ($app, $moduleName) {
			$em = $app['orm.em'];
			$moduleViewPath = str_replace('.', '/', $moduleName);
			
			return new RestController($app, $moduleName, $moduleViewPath, $em->getRepository('BDF2\Content\Entity\Category'), $app[$moduleName . '.form_provider']);
		});
		
		// Setup routing
		$app[$moduleName . '.routes.prefix'] = '/categories';
		
		// Setup resources managed by module
		$app[$moduleName . '.resource_provider'] = $app->protect(function($id) use ($app) {
			if ($id != null)
			{
				$entityManager = $app['orm.em'];

				return $entityManager->getRepository('BDF2\Content\Entity\Category')->findOneById($id);
			}

			return null;
		});
		
		// Setup form
		$app[$moduleName . '.form_provider'] = $app->protect(function($resource) use ($app) {
			return $app['form.factory']->create(new CategoryType($app['form.data_transformer.date_time']), $resource);
		});

		// Adding entities to ORM Entity Manager
		$app['orm.em.paths'] = $app->share($app->extend('orm.em.paths', function($paths) use ($app) {
			$path = __DIR__ . '/../Entity';
			
			if (!in_array($path, $paths, true)) {
				$paths[] = $path;
			}
			
			return $paths;
		}));

		// Adding view paths
		$app['twig.path'] = $app->share($app->extend('twig.path', function($paths) {
			$path = __DIR__ . '/../views';
			
			if (!in_array($path, $paths, true)) {
				$paths[] = $path;
			}
			
			return $paths;
		}));
	}

	public function boot(Application $app) {
		// for standalone version
		$app->mount($app[$this->moduleName . '.routes.prefix'], $app[$this->moduleName . '.module_provider']);
	}

}
