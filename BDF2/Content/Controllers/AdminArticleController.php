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

	public function editAction($resource) {
		
		if ($resource == null)
		{
			$this->app->abort(404, "Artykuł nie istnieje.");
		}
		
		$form = $this->app['content.article.form']($resource);

		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager = $this->app['orm.em'];
				$entityManager->persist($resource);
				$entityManager->flush();
				
				return $this->app->redirect($this->app['url_generator']->generate('content:admin:article:edit', array('resource' => $resource->getId())));
			}
		}

		return $this->render('admin/article/edit.html', array(
			'pageTitle' => 'Edycja artykułu',
			'form' => $form->createView(),
			'article' => $resource,
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
				
				return $this->app->redirect($this->app['url_generator']->generate('content:admin:article:edit', array('resource' => $resource->getId())));
			}
		}
		
		return $this->render('admin/article/edit.html', array(
			'pageTitle' => 'Dodaj artykuł',
			'form' => $form->createView(),
		));
	}
	
	public function removeAction($resource)
	{
		$entityManager = $this->app['orm.em'];
		
		$entityManager->remove($resource);
		$entityManager->flush();
		
		return $this->app->redirect($this->app['url_generator']->generate('content:admin:article:list'));
	}
	
	public function historyAction($resource) {
		$entityManager = $this->app['orm.em'];
		
		$repo = $entityManager->getRepository('Gedmo\Loggable\Entity\LogEntry');
		$logs = $repo->getLogEntries($resource);
		
		return $this->render('admin/article/history.html', array(
			'pageTitle' => 'Historia zmian artykułu',
			'history' => $logs,
		));
	}
	
	public function revertAction($resource, $version) {
		$entityManager = $this->app['orm.em'];
		
		$repo = $entityManager->getRepository('Gedmo\Loggable\Entity\LogEntry');
		$repo->revert($resource, $version);
		
		$this->app['twig']->addGlobal('message', "Dane artykułu zostały wczytane z wersji v{$version} jeśli chcesz przywrócić artykuł w tej postaci wciśnij zapisz.");
		
		return $this->editAction($resource);
	}
}
