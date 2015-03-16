<?php
namespace BDF2\Navigation\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use BDF2\Controllers\AbstractController;
use BDF2\Navigation\Entity\Menu;
use BDF2\Navigation\Form\Type\MenuType;


class AdminMenuController extends AbstractController {
	
	public function listAction()
	{
		$entityManager = $this->app['orm.em'];
		
		return $this->render('admin/menu/list.html', array(
			'pageTitle' => 'Lista menu',
			'menus' => $entityManager->getRepository('BDF2\Navigation\Entity\Menu')->findAll()
		));
	}
	
	public function editAction($id)
	{
		$entityManager = $this->app['orm.em'];

		$resource = $entityManager->getRepository('BDF2\Navigation\Entity\Menu')->findOneById($id);

		if ($resource == null)
		{
			$this->app->abort(404, "Artykuł id:$id nie istnieje.");
		}

		$form = $this->app['navigation.menu.form']($resource);

		if ($this->app['request']->getMethod() == 'POST')
		{
			$form->bind($this->app['request']);

			if ($form->isValid())
			{
				$entityManager->persist($resource);
				$entityManager->flush();
			}
		}

		return $this->render('admin/menu/edit.html', array(
			'pageTitle' => 'Edycja menu',
			'form' => $form->createView(),
		));
	}
	
	public function addAction()
	{
		$entityManager = $this->app['orm.em'];
		
		$resource = new Menu();
		
		$form = $this->app['navigation.menu.form']($resource);
		
		if ($this->app['request']->getMethod() == 'POST')
		{
			$form->bind($this->app['request']);

			if ($form->isValid())
			{
				$entityManager->persist($resource);
				$entityManager->flush();
				
				return $this->app->redirect($this->app['url_generator']->generate('navigation:admin:menu:edit', array('id' => $resource->id)));
			}
		}
		
		return $this->render('admin/menu/edit.html', array(
			'pageTitle' => 'Dodaj menu',
			'form' => $form->createView(),
		));
	}
	
	public function removeAction($id)
	{
		$entityManager = $this->app['orm.em'];
		
		$resource = $entityManager->getRepository('BDF2\Navigation\Entity\Menu')->findOneById($id);
		
		$entityManager->remove($resource);
		$entityManager->flush();
		
		return $this->app->redirect($this->app['url_generator']->generate('navigation:admin:menu:list'));
	}
}
