<?php
namespace BDF2\Avatar\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class AvatarServiceProvider implements ServiceProviderInterface
{
	
	public function register(Application $app) {
		$app['helpers.avatar.provider'] = $app->share(function() use ($app) {
			return new AvatarPriorityProvider();
		});
		
		if (isset($app['twig']))
		{
			$app['twig'] = $app->share($app->extend('twig', function($twig) use($app) {
				$twig->addFunction(new \Twig_SimpleFunction('avatar', function ($id, $size) use ($app) {
					return $app['helpers.avatar.provider']->getAvatar($id, $size);
				}));
			
					return $twig;
			}));
		}
	}

	public function boot(Application $app) {
	}
}