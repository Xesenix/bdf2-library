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

		// Adding view paths
		$app['twig.path'] = $app->share($app->extend('twig.path', function($paths) {
			$paths[] = __DIR__ . '/../views';

			return $paths;
		}));
	}

	public function boot(Application $app) {
	}

}
