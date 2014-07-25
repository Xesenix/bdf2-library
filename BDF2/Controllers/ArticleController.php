<?php

namespace BDF2\Controllers
{
	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	use BDF2\Content\Entity\Article;
	
	class ArticleController {
		public function listAction(Application $app)
		{
			$entityManager = $app['em'];
			
			return $app['twig']->render('article/list.html', array('pageTitle' => 'Test listy artykułów', 'articles' => $entityManager->getRepository('BDF2\Content\Entity\Article')->findAll()));
		}
		
		public function articleAction(Application $app, Request $request)
		{
			$slug = $request->get('slug');
			
			$entityManager = $app['em'];
			
			$article = $entityManager->getRepository('BDF2\Content\Entity\Article')->findOneBy(array('slug' => $slug));
			
			if (!empty($article))
			{
				return $app['twig']->render('article/article.html', array('pageTitle' => $article->getTitle(), 'article' => $article));
			}
			
			$app->abort(404, "Artykuł $slug nie istnieje.");
		}
	}
}