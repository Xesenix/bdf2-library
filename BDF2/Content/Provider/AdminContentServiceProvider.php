<?php

namespace BDF2\Content\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Content\Form\Type\ArticleType;
use BDF2\Content\Controllers\AdminArticleController;
use BDF2\Content\Form\Type\CategoryType;
use BDF2\Content\Controllers\AdminCategoryController;

class AdminContentServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{
	protected $app = null;
	
	protected $moduleName = 'content_admin';
	
	protected $moduleResources = array('article', 'category');
	
	public function connect(Application $app) {
		$module = $app['controllers_factory'];
		
		$articleController = $app[$this->moduleName . '.category.controller_provider'];
		//$articleController->bind("{$this->moduleName}:category");
		$module->mount($app[$this->moduleName . '.category.routes_prefix'], $articleController);
		
		$categoryController = $app[$this->moduleName . '.article.controller_provider'];
		//$categoryController->bind("{$this->moduleName}:article");
		$module->mount($app[$this->moduleName . '.article.routes_prefix'], $categoryController);
		
		return $module;
	}
	
	public function addAdminBasicRouting(&$module, $resourceName)
	{
		$routePrefix =  $this->moduleName . ':' . $resourceName;
		$controllerPrefix = $this->moduleName . '.' . $resourceName;
		
		$module->match('/', "$controllerPrefix.controller:listAction")->bind("$routePrefix:list");
		$module->match('/add', "$controllerPrefix.controller:addAction")->bind("$routePrefix:add");
		$module->match('/remove/{resource}', "$controllerPrefix.controller:removeAction")->bind("$routePrefix:remove");
		$module->match('/{resource}', "$controllerPrefix.controller:editAction")->bind("$routePrefix:edit");
	}
	
	public function addAdminHistoryRouting(&$module, $resourceName)
	{
		$routePrefix =  $this->moduleName . ':' . $resourceName;
		$controllerPrefix = $this->moduleName . '.' . $resourceName;
		
		$module->match('/history/{resource}', "$controllerPrefix.controller:historyAction")->bind("$routePrefix:history");
		$module->match('/{resource}v{version}', "$controllerPrefix.controller:revertAction")->bind("$routePrefix:revert");
	}
	
	public function addBasicConverters(&$module, $resourceName)
	{
		$prefix = $this->moduleName . '.' . $resourceName;
		
		$module->convert('resource', $this->app["$prefix.provider"]);
	}
	
	public function addBasicMiddlewares(&$module, $resourceName)
	{
		$module->convert('resource', $this->app["$prefix.provider"]);
	}
	
	public function registerDefaultResourceControllerProvidersFactories($app, $resources, $factory)
	{
		$app[$this->moduleName . '.controller_provider_factory'] = $factory;
		
		foreach ($resources as $resourceName) {
			$app[$this->moduleName . '.' . $resourceName . '.controller_provider_factory'] = $factory;
		}
	}
	
	public function registerResourceControllerProviders($app, $resources)
	{
		$moduleProvider = $this;
		$moduleName = $this->moduleName;
		foreach ($resources as $resourceName) {
			$app[$moduleName . '.' . $resourceName . '.controller_provider'] = $app->share(function() use($app, $moduleProvider, $resourceName, $moduleName) {
				return $app[$moduleName . '.' . $resourceName . '.controller_provider_factory']($app, $moduleProvider, $resourceName);
			});
		}
	}

	public function register(Application $app) {
		$moduleProvider = $this;
		$this->app = $app;
		
		$app[$this->moduleName . '.module_provider'] = $this;
		
		// Checking for dependencies
		if (!isset($app['orm.em']))
		{
			throw new \RuntimeException('You must register ORM EntityManager before registring ' . get_class($this));
		}

		// Setup Controllers
		$app[$this->moduleName . '.article.controller'] = $app->share(function() use ($app) {
			return new AdminArticleController($app);
		});
		
		$app[$this->moduleName . '.category.controller_provider'] = $app->share(function() use ($app) {
			return new AdminCategoryController($app);
		});
		
		// Setup Controller Providers
		$this->registerDefaultResourceControllerProvidersFactories($app, $this->moduleResources, $app->protect(function($app, $moduleProvider, $resourceName) {
			$module = $app['controllers_factory'];
			
			$moduleProvider->addAdminBasicRouting($module, $resourceName);
			$moduleProvider->addAdminHistoryRouting($module, $resourceName);
			$moduleProvider->addBasicConverters($module, $resourceName);
			
			return $module;
		}));
		
		$this->registerResourceControllerProviders($app, $this->moduleResources);
		
		// Setup routing
		$app[$this->moduleName . '.routes_prefix'] = '/content';
		$app[$this->moduleName . '.article.routes_prefix'] = '/articles';
		$app[$this->moduleName . '.category.routes_prefix'] = '/categories';

		$app[$this->moduleName . '.article.provider'] = $app->protect(function($id) use ($app) {
			if ($id != null)
			{
				$entityManager = $app['orm.em'];

				return $entityManager->getRepository('BDF2\Content\Entity\Article')->findOneById($id);
			}

			return null;
		});
		
		$app[$this->moduleName . '.category.provider'] = $app->protect(function($id) use ($app) {
			if ($id != null)
			{
				$entityManager = $app['orm.em'];

				return $entityManager->getRepository('BDF2\Content\Entity\Category')->findOneById($id);
			}

			return null;
		});

		// Setup form
		$app[$this->moduleName . '.article.form'] = $app->protect(function($resource) use ($app) {
			return $app['form.factory']->create(new ArticleType($app['form.data_transformer.date_time']), $resource);
		});
		
		$app[$this->moduleName . '.category.form'] = $app->protect(function($resource) use ($app) {
			return $app['form.factory']->create(new CategoryType(), $resource);
		});

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
		$app->mount($app[$this->moduleName . '.routes_prefix'], $this);//->bind($this->moduleName);
	}

}
