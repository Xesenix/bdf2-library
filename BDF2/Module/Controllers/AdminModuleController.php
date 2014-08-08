<?php
namespace BDF2\Module\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use BDF2\Controllers\AbstractController;
use BDF2\Module\Entity\Module;

class AdminModuleController extends AbstractController
{

	public function dashboardAction() {
		$entityManager = $this->app['orm.em'];

		return $this->render('module/dashboard.html', array(
			'pageTitle' => 'Tablica modułów',
			'modules' => $entityManager->getRepository('BDF2\Module\Entity\Module')->findAll(),
		));
	}

	public function installAction() {
		$entityManager = $this->app['orm.em'];

		return $this->render('module/install.html', array('pageTitle' => 'Instalacja modułów', ));
	}
	
	public function listAction() {
		$entityManager = $this->app['orm.em'];

		return $this->render('admin/module/list.html', array(
			'pageTitle' => 'Lista modułów',
			'modules' => $entityManager->getRepository('BDF2\Module\Entity\Module')->findAll(),
		));
	}

	public function editAction() {
		$entityManager = $this->app['orm.em'];

		$id = $this->request->get('id');

		$resource = $entityManager->getRepository('BDF2\Module\Entity\Module')->findOneById($id);

		if ($resource == null)
		{
			$this->app->abort(404, "Moduł id:$id nie istnieje.");
		}

		$form = $this->app['module.module.form']($resource);

		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager->persist($resource);
				$entityManager->flush();
			}
		}

		return $this->render('admin/module/edit.html', array(
			'pageTitle' => 'Edycja modułu',
			'form' => $form->createView(),
		));
	}
	
	public function addAction()
	{
		$entityManager = $this->app['orm.em'];
		
		$resource = new Module();
		
		$form = $this->app['module.module.form']($resource);
		
		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager->persist($resource);
				$entityManager->flush();
				
				return $this->app->redirect($this->app['url_generator']->generate('module:admin:module:edit', array('id' => $resource->id)));
			}
		}
		
		return $this->render('admin/module/edit.html', array(
			'pageTitle' => 'Dodaj artykuł',
			'form' => $form->createView(),
		));
	}
	
	public function removeAction($id)
	{
		$entityManager = $this->app['orm.em'];
		
		$resource = $entityManager->getRepository('BDF2\Module\Entity\Module')->findOneById($id);
		
		$entityManager->remove($resource);
		$entityManager->flush();
		
		return $this->app->redirect($this->app['url_generator']->generate('module:admin:module:list'));
	}

}
