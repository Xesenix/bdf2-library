<?php
namespace BDF2\TinyMCE\Provider
{
	use Silex\Application;
	use Silex\ServiceProviderInterface;
	
	class TinyMceServiceProvider implements ServiceProviderInterface {
		
		public function register(Application $app)
		{
			$app['resources.paths'] = $app->share($app->extend('resources.paths', function ($paths) {
				$paths[] = __DIR__ . '/../resources';
				
				return $paths;
			}));
		}

	    public function boot(Application $app)
	    {
	    }
	}
}
