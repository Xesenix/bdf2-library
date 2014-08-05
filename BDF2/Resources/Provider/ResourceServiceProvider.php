<?php
namespace BDF2\Resources\Provider
{
	use Symfony\Component\Config\FileLocator;
	use Symfony\Component\Config\Loader\LoaderResolver;
	use Symfony\Component\Config\Loader\DelegatingLoader;
	use Silex\Application;
	use Silex\ServiceProviderInterface;
	use Silex\ControllerProviderInterface;
	use BDF2\Resources\PathHelper;
	use BDF2\Resources\Loader\AssetLoader;
	use BDF2\Resources\Controllers\AssetController;
	
	class ResourceServiceProvider implements ServiceProviderInterface, ControllerProviderInterface {
		
		public function connect(Application $app)
		{
			$module = $app['controllers_factory'];
			
			$module->match($app['resources.routes.asset'], 'resources.controllers.resources_controller:generateJsAction')->bind('resource:js')->assert('file', '[\w-\._/]+\.js');
			$module->match($app['resources.routes.asset'], 'resources.controllers.resources_controller:generateCssAction')->bind('resource:css')->assert('file', '[\w-\._/]+\.css');
			$module->match($app['resources.routes.asset'], 'resources.controllers.resources_controller:generateAssetAction')->bind('resource:image')->assert('file', '[\w-\._/]+\.(jpg|png|gif)');
			$module->match($app['resources.routes.asset'], 'resources.controllers.resources_controller:generateAssetAction')->bind('resource:asset')->assert('file', '[\w-\._/]+');
			$module->match($app['resources.routes.clear'], 'resources.controllers.resources_controller:clearAction')->bind('resource:clear')->value('path', '/');
			
			return $module;
		}
		
		public function register(Application $app)
		{
			$app['resources.asset.publish_mode'] = false;
			
			$app['path.helper'] = $app->share(function() {
				return new PathHelper();
			});
			
			// Setup Controllers
			$app['resources.controllers.resources_controller'] = $app->share(function() use($app) {
				return new AssetController($app);
			});
			
			// route for resources
			$app['resources.routes.prefix'] = '/resources';
			$app['resources.routes.asset'] = '/{file}';
			$app['resources.routes.clear'] = '/clear{path}';
			
			// relative path to resource directory on public accessible path
			$app['resources.path.public'] = '/resources';
			
			// file system path to resources directory available to public view
			$app['resources.asset.path'] = $app->share(function() use($app) {
				return $app['path.helper']->joinPaths($app['path.root'], $app['resources.routes.prefix']);
			});
			
			// file system paths to asset sources folders inside project and its modules
			$app['resources.paths'] = $app->share(function() {
				return array();
			});
			
			// helper for finding asset sources
			$app['resources.asset.locator'] = $app->share(function() use($app) {
				return new FileLocator($app['resources.paths']);
			});
			
			// helpers for asset loading
			$app['resources.asset.loader'] = $app->share(function() use($app) {
				$loader = new AssetLoader($app['resources.asset.locator'], $app['resources.asset.path'], $app['path.helper']);
				$loader->publishMode($app['resources.asset.publish_mode']);
				
				return $loader;
			});
		}

	    public function boot(Application $app)
	    {
			$app->mount($app['resources.routes.prefix'], $this);
	    }
	}
	
}
