<?php
namespace BDF2\ORM\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Gedmo\DoctrineExtensions;
use Gedmo\Loggable\LoggableListener;

class ORMServiceProvider implements ServiceProviderInterface
{

	public function register(Application $app) {
		$app['orm.em.paths'] = $app->share(function() {
			return array();
		});
		
		$app['orm.event_manager'] = $app->share(function() use($app) {
			return new EventManager();
		});
		
		$app['orm.config'] = $app->share(function() use($app) {
			return Setup::createConfiguration($app['debug']);
		});
		
		$app['orm.anotation_reader'] = $app->share(function() use($app) {
			$annotationReader = new AnnotationReader();
			$cache = $app['orm.config']->getMetadataCacheImpl();
			
			return new CachedReader($annotationReader, $cache);
		});
		
		$app['orm.default_anotation_driver'] = $app->share(function() use($app) {
			AnnotationRegistry::registerFile($app['vendor_dir'] . '/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
			
			return new AnnotationDriver($app['orm.anotation_reader'], $app['orm.em.paths']);
		});

		$app['orm.em'] = $app->share(function() use ($app) {
			
			$annotationReader = $app['orm.anotation_reader'];
			$eventManager = $app['orm.event_manager'];
			
			$driverChain = new MappingDriverChain();
			$driverChain->setDefaultDriver($app['orm.default_anotation_driver']);
			
			DoctrineExtensions::registerMappingIntoDriverChainORM(
				$driverChain,
				$annotationReader
			);
			
			$loggableListener = new LoggableListener;
			$loggableListener->setAnnotationReader($annotationReader);
			$loggableListener->setUsername('admin');
			$eventManager->addEventSubscriber($loggableListener);
			
			$config = $app['orm.config'];
			$config->setMetadataDriverImpl($driverChain);
			
			return EntityManager::create($app['db.default_options'], $config, $eventManager);
		});
	}

	public function boot(Application $app) {
	}

}
