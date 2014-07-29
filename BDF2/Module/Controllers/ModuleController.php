<?php
namespace BDF2\Module\Controllers
{
	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	
	class ModuleController {
		
		public function __construct(Application $app)
		{
			$this->app = $app;
			$this->request = $app['request'];
		}
		
		public function dashboardAction()
		{
			$entityManager = $this->app['orm.em'];
			
			return $this->app['twig']->render('module/dashboard.html', array(
				'pageTitle' => 'Tablica modułów',
				'modules' => $entityManager->getRepository('BDF2\Module\Entity\Module')->findAll(),
			));
		}
		
		public function installAction()
		{
			$entityManager = $this->app['orm.em'];
			
			return $this->app['twig']->render('module/install.html', array(
				'pageTitle' => 'Instalacja modułów',
			));
		}
	}
}
