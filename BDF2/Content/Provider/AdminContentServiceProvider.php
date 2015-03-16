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
		
		$module->mount($app['category_admin.routes_prefix'], $app['category_admin.controller_provider']->connect($app));
		$module->mount($app['article_admin.routes_prefix'], $app['article_admin.controller_provider']->connect($app));
		
		return $module;
	}

	public function register(Application $app) {
		$moduleProvider = $this;
		$this->app = $app;
		
		// Setup controller provider
		$app[$this->moduleName . '.module_provider'] = $this;
		
		// Setup controllers
		$app[$this->moduleName . '.article.controller'] = $app->share(function() use ($app) {
			return new AdminArticleController($app);
		});
		
		$app[$this->moduleName . '.category.controller_provider'] = $app->share(function() use ($app) {
			return new AdminCategoryController($app);
		});
		
		// Register submodules
		$articleServiceProvider = new AdminArticleRestServiceProvider();
		$articleServiceProvider->register($app);
		
		$categoryServiceProvider = new AdminCategoryRestServiceProvider();
		$categoryServiceProvider->register($app);
		
		// Setup routing
		$app[$this->moduleName . '.routes_prefix'] = '/content';
	}

	public function boot(Application $app) {
		$app->mount($app[$this->moduleName . '.routes_prefix'], $this);
	}

}
