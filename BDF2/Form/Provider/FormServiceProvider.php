<?php

namespace BDF2\Form\Provider
{
	use Silex\Application;
	use Silex\ServiceProviderInterface;
	use Pimple\Container;
	
	class FormServiceProvider implements ServiceProviderInterface {
		
		public function register(Application $app)
		{
			$app['form.data_transformer.DateTime'] = $app->share(function() use($app) {
				return new \BDF2\Form\Transformer\DateTime($app['date.default_format']);
			});
			
			$app['form.data_transformer'] = $app->protect(function($name) use($app) {
				$label = 'form.data_transformer.' . $name;
				
				if (!isset($app[$label]))
				{
					$className = "BDF2\\Form\\Transformer\\$name";
					$app[$label] = new $className();
				}
				
				return $app[$label];
			});
		}

	    public function boot(Application $app)
	    {
	    }
	}
}