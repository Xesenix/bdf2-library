<?php
namespace BDF2\Module\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use BDF2\Controllers\AbstractController;

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

	public function editAction() {
		$entityManager = $this->app['orm.em'];

		$id = $this->request->get('id');

		$module = $entityManager->getRepository('BDF2\Module\Entity\Module')->findOneBy(array('id' => $id));

		if ($module == null)
		{
			$this->app->abort(404, "Moduł id:$id nie istnieje.");
		}

		$form = $this->app['module.form']($module);

		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager->persist($module);
				$entityManager->flush();
			}
		}

		return $this->render('edit.html', array(
			'pageTitle' => 'Edycja modułu',
			'form' => $form->createView(),
		));
	}

}
