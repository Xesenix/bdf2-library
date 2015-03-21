<?php

namespace BDF2\Content\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use BDF2\Controllers\AbstractController;
use BDF2\Content\Entity\Article;

class ArticleController extends AbstractController
{

	public function listAction() {
		$entityManager = $this->app['orm.em'];
		
		$this->app['dispatcher']->dispatch('articles:render');
		
		return $this->render('article/list.html', array(
			'pageTitle' => 'Test listy artykułów',
			'articles' => $entityManager->getRepository('BDF2\Content\Entity\Article')->findAll()
		));
	}

	public function articleAction() {
		$entityManager = $this->app['orm.em'];

		$slug = $this->app['request']->get('slug');

		$article = $entityManager->getRepository('BDF2\Content\Entity\Article')->findOneBy(array('slug' => $slug));
		
		$this->app['dispatcher']->dispatch('article:render');
		
		if (!empty($article))
		{
			return $this->render('article/article.html', array(
				'pageTitle' => $article->getTitle(),
				'article' => $article,
			));
		}

		$this->app->abort(404, "Artykuł $slug nie istnieje.");
	}

}
