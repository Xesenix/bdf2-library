<?php
namespace BDF2\ORM\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class ORMServiceProvider implements ServiceProviderInterface
{

	public function register(Application $app) {
		$app['orm.em.paths'] = $app->share(function() {
			return array();
		});

		$app['orm.em'] = $app->share(function() use ($app) {
			$config = Setup::createAnnotationMetadataConfiguration($app['orm.em.paths'], $app['debug']);
			$entityManager = EntityManager::create($app['db.default_options'], $config);

			return $entityManager;
		});
	}

	public function boot(Application $app) {
	}

}
