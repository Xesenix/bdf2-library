<?php
namespace BDF2\Widget\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use BDF2\Controllers\AbstractController;
use BDF2\Widget\Entity\Widget;

class AdminWidgetController extends AbstractController
{

	public function listAction() {
		$entityManager = $this->app['orm.em'];

		return $this->render('admin/widget/list.html', array(
			'pageTitle' => 'Lista wihajstrów',
			'widgets' => $entityManager->getRepository('BDF2\Widget\Entity\Widget')->findAll(),
		));
	}

	public function editAction() {
		$entityManager = $this->app['orm.em'];

		$id = $this->request->get('id');

		$resource = $entityManager->getRepository('BDF2\Widget\Entity\Widget')->findOneById($id);

		if ($resource == null)
		{
			$this->app->abort(404, "Moduł id:$id nie istnieje.");
		}

		$form = $this->app['widget.widget.form']($resource);

		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager->persist($resource);
				$entityManager->flush();
				
				return $this->app->redirect($this->app['url_generator']->generate('widget:admin:widget:edit', array('id' => $resource->id)));
			}
		}

		return $this->render('admin/widget/edit.html', array(
			'pageTitle' => 'Edycja wihajstrów',
			'form' => $form->createView(),
		));
	}
	
	public function addAction()
	{
		$entityManager = $this->app['orm.em'];
		
		$resource = new Widget();
		
		$form = $this->app['widget.widget.form']($resource);
		
		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager->persist($resource);
				$entityManager->flush();
				
				return $this->app->redirect($this->app['url_generator']->generate('widget:admin:widget:edit', array('id' => $resource->id)));
			}
		}
		
		return $this->render('admin/widget/edit.html', array(
			'pageTitle' => 'Dodaj wihajster',
			'form' => $form->createView(),
		));
	}
	
	public function removeAction($id)
	{
		$entityManager = $this->app['orm.em'];
		
		$resource = $entityManager->getRepository('BDF2\Widget\Entity\Widget')->findOneById($id);
		
		$entityManager->remove($resource);
		$entityManager->flush();
		
		return $this->app->redirect($this->app['url_generator']->generate('widget:admin:widget:list'));
	}

}
