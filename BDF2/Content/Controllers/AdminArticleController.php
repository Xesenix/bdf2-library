<?php

namespace BDF2\Content\Controllers
{
	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	use BDF2\Content\Entity\Article;
	use BDF2\Content\Form\Type\ArticleType;
	
	class AdminArticleController {
		public function listAction(Application $app)
		{
			$entityManager = $app['orm.em'];
			
			return $app['twig']->render('article/list.html', array(
				'pageTitle' => 'Lista artykułów',
				'articles' => $entityManager->getRepository('BDF2\Content\Entity\Article')->findAll())
			);
		}
		
		public function editAction(Application $app, Request $request)
		{
			$entityManager = $app['orm.em'];
			
			$id = $request->get('id');
			
			$article = $entityManager->getRepository('BDF2\Content\Entity\Article')->findOneBy(array('id' => $id));
			
			if ($article == null)
			{
				$app->abort(404, "Artykuł id:$id nie istnieje.");
			}
			
			$form = $app['form.factory']->create(new ArticleType($app['form.data_transformer.date_time']), $article);
			
			if ($request->getMethod() == 'POST')
			{
				$form->bind($request);
				
				if ($form->isValid())
				{
					$entityManager->persist($article);
					$entityManager->flush();
				}
			}
			
			return $app['twig']->render('article/edit.html', array(
				'pageTitle' => 'Edycja artykułu',
				'form' => $form->createView(),
			));
		}
	}
}
