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
	
	protected $moduleName = 'content.admin';
	
	protected $moduleResources = array('static', 'article', 'category');
	
	public function connect(Application $app) {
		$module = $app['controllers_factory'];
		
		$module->mount($app['content.category_admin.routes.prefix'], $app['content.category_admin.module_provider']->connect($app));
		$module->mount($app['content.article_admin.routes.prefix'], $app['content.article_admin.module_provider']->connect($app));
		
		return $module;
	}

	public function register(Application $app) {
		$moduleProvider = $this;
		$this->app = $app;
		
		// Setup controller provider
		$app[$this->moduleName . '.module_provider'] = $this;
		
		// Register submodules
		$articleServiceProvider = new AdminArticleRestServiceProvider();
		$articleServiceProvider->register($app);
		
		$categoryServiceProvider = new AdminCategoryRestServiceProvider();
		$categoryServiceProvider->register($app);
		
		// Setup routing
		$app[$this->moduleName . '.routes.prefix'] = '/content';
	}

	public function boot(Application $app) {
		$app->mount($app[$this->moduleName . '.routes.prefix'], $app[$this->moduleName . '.module_provider']);
	}

}
