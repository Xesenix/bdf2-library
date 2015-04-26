<?php
namespace BDF2\Resources\Provider;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use BDF2\Resources\PathHelper;
use BDF2\Resources\Loader\AssetLoader;
use BDF2\Resources\Loader\CompositionLoader;
use BDF2\Resources\Loader\DelegatingLoader;
use BDF2\Resources\Controllers\AssetController;

class ResourceServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{

	public function connect(Application $app) {
		$module = $app['controllers_factory'];
		
		$module->match($app['resources.assets.routes.clear'], 'resources.assets.controller:clearAction')
			->bind('resource:clear')->value('path', '/');
		
		$module->match($app['resources.assets.routes.asset'], 'resources.assets.controller:generateJsAction')
			->bind('resource:js')
			->assert('file', '[\w-\._/]+\.js');
		
		$module->match($app['resources.assets.routes.asset'], 'resources.assets.controller:generateCssAction')
			->bind('resource:css')
			->assert('file', '[\w-\._/]+\.css');
		
		$module->match($app['resources.assets.routes.asset'], 'resources.assets.controller:generateAssetAction')
			->bind('resource:image')
			->assert('file', '[\w-\._/]+\.(' . implode('|', $app['resources.assets.images.extensions']) . ')');
		
		$module->match($app['resources.assets.routes.asset'], 'resources.assets.controller:generateAssetAction')
			->bind('resource:asset')
			->assert('file', '[\w-\._/]+');
		
		return $module;
	}

	public function register(Application $app) {
		$app['resources.assets.publish_mode'] = false;

		$app['path.helper'] = $app->share(function() {
			return new PathHelper();
		});
		
		$app['resources.assets.images.extensions'] = $app->share(function() {
			return array(
				'jpg',
				'png',
				'gif',
				'ico',
			);
		});

		// Setup Controllers
		$app['resources.assets.controller'] = $app->share(function() use ($app) {
			return new AssetController($app);
		});

		// route for resources
		$app['resources.assets.routes.prefix'] = '/resources';
		$app['resources.assets.routes.asset'] = '/{file}';
		$app['resources.assets.routes.clear'] = '/clear{path}';

		/**
		 * file system path to resources directory available to public view
		 * to take advantage of static serving of published files by server
		 * they should be published to url that either reasembles route to publishing service action
		 * or is redirected in htaccess to publishing service
		 *
		 * for example asset is published on:
		 * http://example.com/css/default.css
		 * this route should be resolvable to resource:css route in this case resource controller
		 * should be mounted to $app['resources.assets.routes.prefix'] = '/'
		 */
		$app['resources.assets.public_dir'] = $app->share(function() use ($app) {
			return $app['path.helper']->joinPaths($app['public_dir'], $app['resources.assets.routes.prefix']);
		});

		// file system paths to asset sources folders inside project and its modules
		$app['resources.assets.resource_dir'] = $app->share(function() {
			return array();
		});
		
		// asset compositions
		$app['resources.assets.compositions'] = $app->share(function() use ($app) {
			return array();
		});

		// helper for finding asset sources
		$app['resources.assets.locator'] = $app->share(function() use ($app) {
			return new FileLocator(array_reverse($app['resources.assets.resource_dir']));
		});

		// helpers for asset loading
		$app['resources.assets.loader'] = $app->share(function() use ($app) {
			$compositionLoader = new CompositionLoader($app['resources.assets.compositions'], $app['resources.assets.locator'], $app['resources.assets.public_dir'], $app['path.helper']);
			$compositionLoader->publishMode($app['resources.assets.publish_mode']);
			
			$loader = new AssetLoader($app['resources.assets.locator'], $app['resources.assets.public_dir'], $app['path.helper']);
			$loader->publishMode($app['resources.assets.publish_mode']);

			$loaderResolver = new LoaderResolver(array($compositionLoader, $loader));
			$delegatingLoader = new DelegatingLoader($loaderResolver);

			return $delegatingLoader;
		});
	}

	public function boot(Application $app) {
		$app->mount($app['resources.assets.routes.prefix'], $this);
	}
}
