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

		$article = $entityManager->getRepository('BDF2\Content\Entity\Article')->findOneById($id);

		if ($article == null)
		{
			$this->app->abort(404, "Artykuł id:$id nie istnieje.");
		}

		$form = $this->app['content.article.form']($article);

		if ($this->request->getMethod() == 'POST')
		{
			$form->bind($this->request);

			if ($form->isValid())
			{
				$entityManager->persist($article);
				$entityManager->flush();
			}
		}

		return $this->render('admin/article/edit.html', array(
			'pageTitle' => 'Edycja artykułu',
			'form' => $form->createView(),
		));
	}

}
