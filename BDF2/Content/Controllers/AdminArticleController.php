<?php

namespace BDF2\Content\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use BDF2\Controllers\AbstractController;
use BDF2\Content\Entity\Article;
use BDF2\Content\Form\Type\ArticleType;

class AdminArticleController extends AbstractController
{

	public function listAction() {
		$entityManager = $this->app['orm.em'];

		return $this->render('admin/article/list.html', array(
			'pageTitle' => 'Lista artykułów',
			'articles' => $entityManager->getRepository('BDF2\Content\Entity\Article')->findAll()
		));
	}

	public function editAction() {
		$entityManager = $this->app['orm.em'];

		$id = $this->request->get('id');

		$resource = $entityManager->getRepository('BDF2\Content\Entity\Article')->findOneById($id);

		if ($resource == null)
		{
			$this->app->abort(404, "Artykuł id:$id nie istnieje.");
		}

		$form = $this->app['content.article.form']($resource);

		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager->persist($resource);
				$entityManager->flush();
			}
		}

		return $this->render('admin/article/edit.html', array(
			'pageTitle' => 'Edycja artykułu',
			'form' => $form->createView(),
		));
	}
	
	public function addAction()
	{
		$entityManager = $this->app['orm.em'];
		
		$resource = new Article();
		
		$form = $this->app['content.article.form']($resource);
		
		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager->persist($resource);
				$entityManager->flush();
				
				return $this->app->redirect($this->app['url_generator']->generate('content:admin:article:edit', array('id' => $resource->getId())));
			}
		}
		
		return $this->render('admin/article/edit.html', array(
			'pageTitle' => 'Dodaj artykuł',
			'form' => $form->createView(),
		));
	}
	
	public function removeAction($id)
	{
		$entityManager = $this->app['orm.em'];
		
		$resource = $entityManager->getRepository('BDF2\Content\Entity\Article')->findOneById($id);
		
		$entityManager->remove($resource);
		$entityManager->flush();
		
		return $this->app->redirect($this->app['url_generator']->generate('content:admin:article:list'));
	}
}
