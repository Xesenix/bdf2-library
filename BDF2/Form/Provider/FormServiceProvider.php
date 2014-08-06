<?php

namespace BDF2\Form\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Pimple\Container;

class FormServiceProvider implements ServiceProviderInterface
{

	public function register(Application $app) {
		$app['form.data_transformer.date_time'] = $app->share(function() use ($app) {
			return new \BDF2\Form\Transformer\DateTime($app['date.default_format']);
		});
	}

	public function boot(Application $app) {
	}

}
