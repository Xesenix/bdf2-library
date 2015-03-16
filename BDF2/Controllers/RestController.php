<?php
namespace BDF2\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class RestController extends AbstractController
{
	protected $resourceRepository;
	
	protected $modulePrefix;
	
	protected $formProvider;

	/**
	 * @param Application $app - di container
	 * @param EntityRepository $resourceRepository - resource entities repository
	 * @param callback $formProvider - provides form for editing resource
	 */
	public function __construct(Application $app, $modulePrefix, $resourceRepository, $formProvider) {
		parent::__construct($app);
		
		$this->modulePrefix = $modulePrefix;
		$this->resourceRepository = $resourceRepository;
		$this->formProvider = $formProvider;
	}

	public function listAction() {
		$entityManager = $this->app['orm.em'];

		return $this->render($this->modulePrefix . '/list.html', array(
			'pageTitle' => 'Lista zasobów',
			'resources' => $this->resourceRepository->findAll()
		));
	}
	
	public function handleRequest() {
		$formProvider = $this->formProvider;
		$form = $formProvider($resource);

		if ($this->app['request']->getMethod() == 'POST')
		{
			$form->submit($this->app['request']);

			if ($form->isValid())
			{
				$resource = $form->getData();
				
				$entityManager = $this->app['orm.em'];
				$entityManager->persist($resource);
				$entityManager->flush();
				
				return $this->app->redirect($this->app['url_generator']->generate($this->modulePrefix . ':edit', array('resource' => $resource->getId())));
			}
		}
		
		return null;
	}
	
	public function editAction($resource) {
		
		if ($resource == null)
		{
			$this->app->abort(404, "Zasób nie istnieje.");
		}
		
		$formProvider = $this->formProvider;
		$form = $formProvider($resource);

		if ($this->app['request']->getMethod() == 'POST')
		{
			$form->handleRequest($this->app['request']);

			if ($form->isValid())
			{
				$resource = $form->getData();
				
				$entityManager = $this->app['orm.em'];
				$entityManager->persist($resource);
				$entityManager->flush();
				
				return $this->app->redirect($this->app['url_generator']->generate($this->modulePrefix . ':edit', array('resource' => $resource->getId())));
			}
		}

		return $this->render($this->modulePrefix . '/edit.html', array(
			'pageTitle' => 'Edycja zasobu',
			'form' => $form->createView(),
			'resource' => $resource,
		));
	}
	
	public function addAction()
	{
		$formProvider = $this->formProvider;
		$form = $formProvider(null);
		
		if ($this->app['request']->getMethod() == 'POST')
		{
			$form->submit($this->app['request']);

			if ($form->isValid())
			{
				$resource = $form->getData();
				
				$entityManager = $this->app['orm.em'];
				$entityManager->persist($resource);
				$entityManager->flush();
				
				return $this->app->redirect($this->app['url_generator']->generate($this->modulePrefix . ':edit', array('resource' => $resource->getId())));
			}
		}
		
		return $this->render($this->modulePrefix . '/edit.html', array(
			'pageTitle' => 'Dodaj zasób',
			'form' => $form->createView(),
		));
	}
	
	public function removeAction($resource)
	{
		$entityManager = $this->app['orm.em'];
		
		$entityManager->remove($resource);
		$entityManager->flush();
		
		return $this->app->redirect($this->app['url_generator']->generate($this->modulePrefix . ':list'));
	}
	
	public function historyAction($resource) {
		$entityManager = $this->app['orm.em'];
		
		$logRepository = $entityManager->getRepository('Gedmo\Loggable\Entity\LogEntry');
		$logs = $logRepository->getLogEntries($resource);
		
		return $this->render($this->modulePrefix . '/history.html', array(
			'pageTitle' => 'Historia zmian zasobu',
			'history' => $logs,
		));
	}
	
	public function revertAction($resource, $version) {
		$entityManager = $this->app['orm.em'];
		
		$logRepository = $entityManager->getRepository('Gedmo\Loggable\Entity\LogEntry');
		$logRepository->revert($resource, $version);
		
		return $this->editAction($resource);
	}
}
