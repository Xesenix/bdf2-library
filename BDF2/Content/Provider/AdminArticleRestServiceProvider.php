<?php

namespace BDF2\Content\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Content\Form\Type\ArticleType;
use BDF2\Content\Controllers\AdminArticleController;

class AdminArticleRestServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{
	protected $app = null;
	
	protected $moduleName = 'article_admin';
	
	public function connect(Application $app) {
		$module = $app['controllers_factory'];
		
		$this->addAdminBasicRouting($module);
		$this->addAdminHistoryRouting($module);
		$this->addBasicConverters($module);
		
		return $module;
	}
	
	public function addAdminBasicRouting(&$module)
	{
		$prefix =  $this->moduleName;
		
		$module->match('/', "$prefix.controller:listAction")->bind("$prefix:list");
		$module->match('/add', "$prefix.controller:addAction")->bind("$prefix:add");
		$module->match('/remove/{resource}', "$prefix.controller:removeAction")->bind("$prefix:remove");
		$module->match('/{resource}', "$prefix.controller:editAction")->bind("$prefix:edit");
	}
	
	public function addAdminHistoryRouting(&$module)
	{
		$prefix =  $this->moduleName;
		
		$module->match('/history/{resource}', "$prefix.controller:historyAction")->bind("$prefix:history");
		$module->match('/{resource}v{version}', "$prefix.controller:revertAction")->bind("$prefix:revert");
	}
	
	public function addBasicConverters(&$module)
	{
		$prefix = $this->moduleName;
		
		$module->convert('resource', $this->app["$prefix.resource_provider"]);
		$module->assert('resource', '\d+');
		$module->assert('version', '\d+');
	}
	
	public function addBasicMiddlewares(&$module)
	{
		
	}

	public function register(Application $app) {
		$this->app = $app;
		
		$app[$this->moduleName . '.module_provider'] = $this;
		
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
		$app[$this->moduleName . '.controller_provider'] = $this;

		// Setup controllers
		$app[$this->moduleName . '.controller'] = $app->share(function() use ($app) {
			return new AdminArticleController($app);
		});
		
		// Setup routing
		$app[$this->moduleName . '.routes_prefix'] = '/articles';

		$app[$this->moduleName . '.resource_provider'] = $app->protect(function($id) use ($app) {
			if ($id != null)
			{
				$entityManager = $app['orm.em'];

				return $entityManager->getRepository('BDF2\Content\Entity\Article')->findOneById($id);
			}

			return null;
		});
		
		// Setup form
		$app[$this->moduleName . '.resource_form'] = $app->protect(function($resource) use ($app) {
			return $app['form.factory']->create(new ArticleType($app['form.data_transformer.date_time']), $resource);
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
		$app->mount($app[$this->moduleName . '.routes_prefix'], $app[$this->moduleName . '.controller_provider']);
	}

}
