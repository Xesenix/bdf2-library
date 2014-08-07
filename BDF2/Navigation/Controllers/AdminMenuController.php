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
	
	public function editAction()
	{
		$entityManager = $this->app['orm.em'];

		$id = $this->request->get('id');

		$menu = $entityManager->getRepository('BDF2\Navigation\Entity\Menu')->findOneById($id);

		if ($menu == null)
		{
			$this->app->abort(404, "Artykuł id:$id nie istnieje.");
		}

		$form = $this->app['navigation.menu.form']($menu);

		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager->persist($menu);
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
		
		$menu = new Menu();
		
		$form = $this->app['navigation.menu.form']($menu);
		
		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager->persist($menu);
				$entityManager->flush();
			}
		}
		
		return $this->render('admin/menu/edit.html', array(
			'pageTitle' => 'Dodaj menu',
			'form' => $form->createView(),
		));
	}
}
