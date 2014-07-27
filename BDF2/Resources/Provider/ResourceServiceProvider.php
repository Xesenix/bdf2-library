<?php
namespace BDF2\Resources\Provider
{
	use Silex\Application;
	use Silex\ServiceProviderInterface;
	use Silex\ControllerProviderInterface;
	use BDF2\Resources\PathHelper;
	use BDF2\Resources\File\FileLocator;
	use BDF2\Resources\Asset\FileAsset;
	
	class ResourceServiceProvider implements ServiceProviderInterface, ControllerProviderInterface {
		
		public function connect(Application $app)
		{
			$module = $app['controllers_factory'];
			
			$module->match($app['routes.resources.css'], 'BDF2\\Resources\Controllers\AssetController::generateCssAction')->bind('css');
			$module->match($app['routes.resources.css_clear'], 'BDF2\\Resources\Controllers\AssetController::clearCssAction')->bind('css-clear')->value('path', '/');
			$module->match($app['routes.resources.js'], 'BDF2\\Resources\Controllers\AssetController::generateJsAction')->bind('js');
			$module->match($app['routes.resources.js_clear'], 'BDF2\\Resources\Controllers\AssetController::clearJsAction')->bind('js-clear')->value('path', '/');
			
			return $module;
		}
		
		public function register(Application $app)
		{
			$app['resources.dev_mode'] = false;
			
			$app['path.helper'] = $app->share(function() {
				return new PathHelper();
			});
			
			// Setup routing
			$app['routes.resources'] = '/resources';
			$app['routes.resources.css'] = '/css/{file}';
			$app['routes.resources.css_clear'] = '/css-clear/{path}';
			$app['routes.resources.js'] = '/js/{file}';
			$app['routes.resources.js_clear'] = '/js-clear/{path}';
			
			// file system path to resources directory aviable to public view
			$app['path.resources'] = $app->share(function() use($app) {
				return $app['path.helper']->joinPaths($app['path.root'], '/resources');
			});
			// relative path to asset directories inside resource folder
			$app['path.resources.css'] = '/css';
			$app['path.resources.js'] = '/js';
			
			// file system paths to asset sources folders inside project and its modules
			$app['resources.paths'] = $app->share(function() {
				return array();
			});
			
			// helper for finding asset sources
			$app['resources.locator'] = $app->share(function() use($app) {
				return new FileLocator($app['resources.paths'], $app['path.helper']);
			});
			
			// helpers for asset manipulation
			$app['resources.asset.css'] = $app->share(function() use($app) {
				$assetRoot = $app['path.helper']->joinPaths($app['path.resources'], $app['path.resources.css']);
				
				$asset = new FileAsset($app['resources.locator'], $assetRoot, $app['path.helper']);
				$asset->setDevMode($app['resources.dev_mode']);
				
				return $asset;
			});
			
			$app['resources.asset.js'] = $app->share(function() use($app) {
				$assetRoot = $app['path.helper']->joinPaths($app['path.resources'], $app['path.resources.js']);
				
				$asset = new FileAsset($app['resources.locator'], $assetRoot, $app['path.helper']);
				$asset->setDevMode($app['resources.dev_mode']);
				
				return $asset;
			});
		}

	    public function boot(Application $app)
	    {
			$app->mount($app['routes.resources'], $this);
	    }
	}
	
}
